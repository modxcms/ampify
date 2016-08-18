# AMPIFY

Turn a MODX Context into a router for Google's [Accelerated Mobile Pages](https://www.ampproject.org/) aka "AMP".

## What is it?

AMPIFY is a MODX Extra that includes a toolset to easily serve AMP HTML for Google's search results pages. The package includes a Plugin, and a sample AMP Template.

## What does it do?

The AMPIFY Plugin is triggered when a request is made to a specified AMP Context. (Context routing and setup are outside the scope of this Extra, but the [documentation](#) provides an example implementation.) 

The AMP Context will automatically route requests to Resources that have been added to the ContextResource table. Resources are added automatically when they are saved. 

If a value is provided in the `amp_tv` Plugin property, then the Resource will only be added to the table if the Resource has a valid Template ID as that TV's value, and in that case, it will override the Template ID in the Plugin's `amp_template` property.

Essentially, AMPIFY is a Template switcher, that uses some magical-secret-fu that Jason Coward and John Peca recently revealed to handle routing and Symlinking.

## Installation

Install via the MODX Revolution Package Manager.

## Usage

The default Plugin property values are:

- `amp_context` = `"amp"` If this property is empty or doesn't contain a valid Context key, the Plugin effectively does nothing.
- `amp_template` = `""` This property is required. At least 1 Template must be dedicated to an AMP view, and it's ID set here, for the Plugin to work.
- `amp_tv` = `""` If a valid TV **name** is entered here, it will transform the Plugin's actions in the following way:
    - `OnLoadWebDocument` it will switch the Template of the Resource to the one specified in the TV with name `amp_tv`, falling back to the default set in `amp_template`
    - `OnDocFormSave` it will _only_ add the Resource to the table for automatic routing if there's a truth-y value in the TV with name `amp_tv`. If the TV is empty or false-y, it will remove the Resource from the table. This provides primitive "remove" functionality until such time a CMP is made to manage ContextResources.
