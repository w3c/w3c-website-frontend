# Front-end integration

Documentation on how to integrate built HTML/CSS templates with content (from a CMS or other data sources) in Symfony.
We are using the [Strata Frontend library](https://github.com/strata/frontend) to help integrate with headless CMS and other data sources.

## Routing and localisation

Routes are setup in Symfony using [annotations](https://symfony.com/doc/current/routing.html#creating-routes-as-attributes-or-annotations) 
in controllers (stored in `src/Controller`).

Locale prefixes are setup in the [annotation config](config/routes/annotations.yaml) file. The following locales are setup:
* /fr - French
* /ja - Japanese
* /zh-hans - Simplified Chinese
* default - American English

All non-locale page URLs below can be served in a locale by prefixing with the locale URL prefix, e.g. `/ja/news`.

### Root URLs served by Symfony application

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

## Retrieving data

### Retrieving CraftCMS content via GraphQL API

For content retrieved from CraftCMS we need to do this using GraphQL queries. We also need to use an authentication token 
to use the _Reading schema_.

We need a few different types of GraphQL queries to do the following:
* Retrieve all content for an individual page
* Retrieve all global content (currently global navigation)
* Retrieve lists fo content for listing pages (e.g. news) 

Find out [how to write GraphQL queries for CraftCMS](craftcms-graphql.md).

### Writing queries

Once we've confirmed our GraphQL query these are saved at `src/Query/CraftCMS/graphql` and have the file extension `.graphql`

Any fragments (complex GraphQL query fragments that can be included in other queries) are stored in `src/Query/CraftCMS/graphql/fragments`.

### Running queries

TODO

## Twig templates

Templates are stored in `templates/`.

### Template bundle

We include the [W3C Symfony template bundle](https://github.com/w3c/w3c-website-templates-bundle) in this project.

TODO

### Generating URLs

See https://symfony.com/doc/current/routing.html#generating-urls

## Testing

TODO

