# AMPIFY

Turn a MODX Context into a router for Google's [Accelerated Mobile Pages](https://www.ampproject.org/) aka "AMP".

## What is it?

AMPIFY is a MODX Extra that includes a toolset to easily serve AMP HTML for Google's search results pages. The package includes a Plugin, and a sample AMP Template.

## What does it do?

The AMPIFY Plugin is triggered when a request is made to a specified AMP Context. Context routing and setup are outside the scope of this Extra, but the [documentation](#) provides an example implementation. The AMP Context will automatically route requests to Resources that have been added to the ContextResource table. Resources are added automatically when they are saved. If a value is provided in the `amp_tv` Plugin property, then the Resource will only be added to the table if the Resource has a valid Template ID as that TV's value, and that Template ID will override the Plugin's `amp_template` property.

Essentially, AMPIFY is a Template switcher, that uses some magical-secret-fu that Jason Coward and John Peca recently revealed.
