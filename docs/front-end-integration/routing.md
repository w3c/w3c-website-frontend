# Routing

A [proposed sitemap](https://docs.google.com/spreadsheets/d/1a9pm5HWzcidtLPCeFRz4F0Ir4TT3oOK54FlEEd3IXUE/edit#gid=315005175) exists in Google Sheets which has been used by W3C and Studio 24 to help plan the web pages for the new w3.org website.

## Symfony

Routes are setup in Symfony using [annotations](https://symfony.com/doc/current/routing.html#creating-routes-as-attributes-or-annotations)
in controllers (stored in `src/Controller`).

## Localisation

Locale prefixes are setup in the [annotation config](config/routes/annotations.yaml) file. The following locales are setup:
* /fr - French
* /ja - Japanese
* /zh-hans - Simplified Chinese
* default - American English

All non-locale page URLs below can be served in a locale by prefixing with the locale URL prefix, e.g. `/ja/news`.

## Root URLs served by Symfony application

Given a wide range of pages are managed at w3.org not all URLs are directed to the Symfony application. Therefore it's
important to be aware there are a limited number of root URLs served by the Symfony application. These are:

* /about
* /ecosystems
* /agreements
* /blog
* /careers
* /contact
* /copyright
* /donate
* /evangelists
* /events
* /feeds
* /fr
* /get-involved
* /help
* /in-the-media
* /ja
* /liaisons
* /membership
* /news
* /newsletter
* /policies
* /press-releases
* /resources
* /sponsor
* /staff
* /standards
* /zh-hans

_Future improvement: look at extracting this URL list into a config file/plugin in CraftCMS so we can highlight to users when they attempt to
add a root URL that is not served by the Symfony application._