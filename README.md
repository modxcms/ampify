# Ampify

Effortless Google [Accelerated Mobile Pages](https://www.ampproject.org/), aka "AMP", for MODX Revolution.

## What is it?

**Ampify** is a MODX Revolution Extra that includes a toolset to easily serve AMP HTML for Google's search results pages. The package includes a Plugin, and a sample AMP Template.

## What does it do?

When triggered, the Plugin switches the Template used by the currently requested Resource, to the specified AMP Template.

## I want details

The Ampify Plugin fires in two different modes: 'param' and 'context'.

### Context Mode

In 'context' mode, the Plugin action is triggered when a request is made to a specified AMP Context. (Context routing and setup are outside the scope of this Extra, but there's an [example implementation](#example-implementation) below.) 

The AMP Context will automatically route requests to Resources that have been added to the ContextResource table. Resources are added automatically when they are saved. In future versions, a CMP will be provided to add/remove Resources from the table, with bulk actions.

This mode has the advantage of being able to include and exclude Resources from having an AMP view. 

However, it has the disadvantage that AMP views will be rendered at a different URI than the canonical. At the minimum, the Context's base_url will be prefixed, for example: `/amp/resource-alias.html` versus `/resource-alias.html`

### Param Mode

In 'param' mode, the Plugin action is triggered when the GET param specified is set, in the URL string. This will override the `amp_context` property. In this mode:

- If a request does not have the GET param specified, the Plugin will not fire, even if the request is in the specified `amp_context`.
- The Plugin action will not be triggered on the `OnDocFormSave` Event, and the ContextResource table will not be modified while in this mode.

This mode has the advantage of rendering the AMP view at the same URI as the requested Resource. Nothing about the Resource's URI needs to change, except the presence of the specified GET param.

However, it has the disadvantage that Resources cannot be specifically included or excluded from having an AMP view—the Plugin will fire if the GET param is present. Also, **the AMP view will not be cacheable**. This can cause serious performance issues, depending on the complexity of the AMP Template used.

A workaround for excluding Resources from the AMP view, would be to use the `amp_tv` property, and for Resources you want to exclude from having an AMP view, set the TV value to the same Template, that the Resource uses natively. However, if the URL param is set, **the Resource will not be cacheable**. There's no workaround in this version of the Extra, for the caching limitation in 'param' mode.

### AMP TV

If a value is provided in the `amp_tv` Plugin property, then the Resource will _only_ be added to the ContextResource table, if the Resource has a valid Template ID set in the TV. In that case, it will override the Template ID in the Plugin's `amp_template` property `OnLoadWebDocument` _for that Resource_.

If the TV is empty for a given Resource, that Resource will be removed from the ContextResource table.

In 'param' mode, no modifications happen to the ContextResource table, but the `amp_tv` value will be used to render the AMP view, falling back to the `amp_template` Plugin property as default.

## Installation

Install via the MODX Revolution Extras Installer (aka "Package Manager").

## Usage

The default Plugin property values are:

- `amp_context` = `""` If this property is empty or doesn't contain a valid Context key, the Plugin will try to run in 'param' mode.
- `amp_url_param` = `""` If this property is set, it will override the `amp_context` property and trigger the AMP Template view if a GET param with the specified key is set in the URL string. If this property is empty and `amp_context` is also empty, the Plugin effectively does nothing.
- `amp_template` = `""` This property is required. At least 1 Template must be dedicated to an AMP view, and that Template's ID set here, in order for the Plugin to work at all.
- `amp_tv` = `""` If a valid TV **name** is entered here, it will transform the Plugin's actions in the following way:
    - `OnLoadWebDocument` it will switch the Template of the Resource to the one specified in the TV with name `amp_tv`, falling back to the default set in `amp_template`
    - `OnDocFormSave` it will _only_ add the Resource to the table for automatic routing if there's a truth-y value in the TV with name `amp_tv`. If the TV is empty or false-y, it will remove the Resource from the table. This provides primitive "remove" functionality until such time a CMP is made to manage ContextResources.

## Example Implementation
