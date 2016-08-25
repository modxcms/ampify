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

Also, if you have a lot of pre-existing Resources for which you want to provide an AMP view, you'll need to add them all to the ContextResource table by triggering the `OnDocFormSave` Event on all of them—or modify the ContextResource table yourself, programmatically. Until the CMP with bulk actions is developed (in a later version soon-to-come) this is a drawback of the 'context' mode.

### Param Mode

In 'param' mode, the Plugin action is triggered when the GET param specified is set, in the URL string. This will override the `amp_context` property. In this mode:

- If a request does not have the GET param specified, the Plugin will not fire, even if the request is in the specified `amp_context`.
- The Plugin action will not be triggered on the `OnDocFormSave` Event, and the ContextResource table will not be modified while in this mode.

This mode has the advantage of rendering the AMP view at the same URI as the requested Resource. Nothing about the Resource's URI needs to change, except the presence of the specified GET param.

Also, the AMP view becomes immediately enabled for all your Resources, without any additional modifications of database records.

However, it has the disadvantage that Resources cannot be specifically included or excluded from having an AMP view—the Plugin will fire for all Resources if the GET param is present. 

Another, perhaps more critical drawback, is that **the AMP view will not be cacheable**. This can cause serious performance issues, depending on the complexity of the AMP Template used. It's true that Google caches the AMP views, but it opens a door for anyone to trigger high loads on your site, if your AMP Template requires processing.

A workaround for excluding Resources from the AMP view, would be to use the `amp_tv` property, and for Resources you want to exclude from having an AMP view, set the TV value to the same Template that the Resource uses natively. However, if the URL param is set, **the Resource will not be cacheable**. There's no workaround in this version of Ampify, for the caching limitation in 'param' mode.

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

In this example, we'll install Ampify into an almost fresh MODX install, and use [Context Gateway](https://github.com/modxcms/context-gateway/) for Context routing. Both can be installed via the Extras Installer:

![Extras Installer](https://www.dropbox.com/s/bjtk7auqs29a2tc/Screenshot%202016-08-24%2013.34.55.png?dl=1)

### Create a new Context. 

Hover over the cog icon in the top-right corner of the Manager, to reveal the "Contexts" menu item.

![Contexts menu item](https://www.dropbox.com/s/q3667nsqlkdftrm/Screenshot%202016-08-24%2013.35.02.png?dl=1)

In the Contexts view, click "Create New".

![Create Context button](https://www.dropbox.com/s/0b1wx1jst8zrzoe/Screenshot%202016-08-24%2013.35.13.png?dl=1)

Give the new Context a key, a user-friendly name, and optionally a description.

![Create Context window](https://www.dropbox.com/s/ij1x80k4kjcw814/Screenshot%202016-08-24%2013.35.24.png?dl=1)

Once the Context is created, you'll see it in the grid-list of Contexts. Right-click on it and select "Update Context".

![Update Context](https://www.dropbox.com/s/xtwbmsga0gywsrl/Screenshot%202016-08-25%2008.34.43.png?dl=1)

In the Context Edit view, select the tab "Context Settings".

![Context Settings tab](https://www.dropbox.com/s/bdom7y9nhdzvwb4/Screenshot%202016-08-25%2008.35.20.png?dl=1)

If you're not familiar with Context Settings, here are the documentation pages for [Contexts](https://rtfm.modx.com/revolution/2.x/administering-your-site/contexts) and [System Settings](https://rtfm.modx.com/revolution/2.x/administering-your-site/settings), which you might find useful to review before moving on to the next step.

Click the "Create New" button. You'll need to create 5 new Context Settings, at a minimum:

1. `ctx_alias` is a requirement of the Context Gateway router, and defines the URI-bit for this Context. You'll use this string again in other settings.

![ctx_alias setting](https://www.dropbox.com/s/maeqsabh6xgm01x/Screenshot%202016-08-24%2013.37.42.png?dl=1)

2. `site_url` overrides the default System Setting, appending the `ctx_alias`.

![site_url Context setting](https://www.dropbox.com/s/h0phk0a3jxecyac/Screenshot%202016-08-24%2013.37.26.png?dl=1)

Notice the syntax `{site_url}amp/`. The part in curly braces references the default MODX `site_url` dynamically.

3. `base_url` does essentially the same as `site_url` but for a specific component of the URL.

![base_url setting](https://www.dropbox.com/s/rrudd40yilex9dr/Screenshot%202016-08-24%2013.37.34.png?dl=1)

4. `site_start` and `error_page` should both have, as their values, the ID of the Resource that MODX will render as the "homepage" of the new Context. If you haven't created a Resource in the new Context yet, you can do so without leaving the current view, by right-clicking on the Context node in the Resource Tree, and selecting the "Quick Create" » "Document" option:

![Quick Create](https://www.dropbox.com/s/g0l2ysnn600goei/Screenshot%202016-08-25%2008.44.35.png?dl=1)

Give the new Resource a title, and ensure it's published. Once you hit save, the Tree will refresh and you'll see the new Resource with it's ID in parenthesis:

![Resource Tree](https://www.dropbox.com/s/0ws3ph6etnwx83p/Screenshot%202016-08-25%2008.47.02.png?dl=1)

### Friendly URL System Setting

If you haven't already enabled Friendly URLs for your site, you'll need to do so for Ampify and Context Gateway to work. Hover over the cog icon again and chooose "System Settings".

![System Settings menu item](https://www.dropbox.com/s/d921uwv7985tnaj/Screenshot%202016-08-25%2008.49.39.png?dl=1)

At the top of the grid, choose the "Friendly URL" option from the "Area" dropdown:

![Area dropdown](https://www.dropbox.com/s/ygzp0f122kxi6nu/Screenshot%202016-08-24%2013.39.08.png?dl=1)

Locate the "Use Friendly URLs" setting and set it to "Yes".

![FURLs](https://www.dropbox.com/s/tldqixi3od1zxbx/Screenshot%202016-08-24%2013.39.17.png?dl=1)

### Plugin Properties

Next you'll setup the AMPIFY Plugin's properties, without which the Plugin doesn't fire. You'll need the ID of the AMP Template that you want to use. Ampify installs a sample Template. 

![sample AMP Template](https://www.dropbox.com/s/4z6y8btmhrlldxk/Screenshot%202016-08-24%2013.40.10.png?dl=1)

In this installation the ID is `4`. In the "Elements" tab of the Tree, open the "Plugins" section if not already open, and locate the AMPIFY Plugin:

![Elements Tree](https://www.dropbox.com/s/pchhh6x8r2ygo6v/Screenshot%202016-08-25%2008.57.17.png?dl=1)

![AMPIFY Plugin edit](https://www.dropbox.com/s/qnlzvgp2ckcpovo/Screenshot%202016-08-24%2013.40.16.png?dl=1)

In the Plugin Edit view, select the "Properties" tab:

![Plugin properties](https://www.dropbox.com/s/4n2v1hlidff6o0d/Screenshot%202016-08-24%2013.41.06.png?dl=1)

Click the button "Add Property Set". It's recommended to use a custom property set that will not be overwritten when you update the Extra.

![Add Property Set](https://www.dropbox.com/s/15azi5ew1qaospd/Screenshot%202016-08-25%2008.57.54.png?dl=1)

In the "Add Property Set" window, select "Create New Property Set" and give it a name:

![Add Property Set Window](https://www.dropbox.com/s/88ykgwu1gaxf8rt/Screenshot%202016-08-24%2013.41.32.png?dl=1)

Hit save. In the property set dropdown, select your new set:

![Dropdown](https://www.dropbox.com/s/m1rpirfjq6ytu84/Screenshot%202016-08-24%2013.41.41.png?dl=1)

Modify the property values, as per your desired configuration. In this implementation, we'll add the Context Key and Template ID:

![Property set for AMP](https://www.dropbox.com/s/yg6o0byqa23o6c0/Screenshot%202016-08-25%2009.00.07.png?dl=1)

Save the property set.

![Save property set](https://www.dropbox.com/s/vaptd1jvv1lptbc/Screenshot%202016-08-25%2009.17.28.png?dl=1)

You'll also need to select the custom property set for the Plugin's Event triggers. Go to the "System Events" tab, and select the custom property set for both enabled Events:

![Plugin Events](https://www.dropbox.com/s/340mkcn5dtua0pf/Screenshot%202016-08-25%2009.19.06.png?dl=1)

Don't forget to "Save" the Plugin!

![Save Plugin](https://www.dropbox.com/s/44rseg3gfbwuzj2/Screenshot%202016-08-25%2009.19.22.png?dl=1)

Now the Plugin should be running in 'context' mode. When you save a Resource, it will be added to the `ContextResource` table for the `amp` Context. The AMP view for the Resource will be a the Resource's URI, prefixed with the `ctx_alias`:  `amp/`.
