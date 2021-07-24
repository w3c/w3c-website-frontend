# Writing GraphQL to retrieve content from CraftCMS

## GraphQL Explorer

It's recommended to use the GraphQLi explorer to help build GraphQL queries. Login to the W3C CMS, go to
_GraphQL > GraphiQL_ and use the _Reading schema_. 

Select _Explorer_ to view a list of all fields available. You can compare this with content entry pages in CraftCMS to
confirm content fields you need to select.

If you are using fragments copy these into the query window. 

If you are using variables copy these into the variables window as JSON, e.g.

```json
{
  "uri": "landing-page/w3c-mission-default",
  "siteId": 1
}
```

## Working out what content fields to retrieve

There are a lot of content fields exposed in GraphQL. To make it slightly easier to work out what to select you can review
the entry types setup in CraftCMS. 

To do this login to the CMS and go to _Settings > Content > Sections_. Find the section you are interested in and select 
the _entry type_ you want to view content fields for. This displays the content configurator and lists all custom fields 
for that entry type.

To view details about a specific content field go to _Settings > Content > Content Fields_.

_**Please note:** we use configuration (in version control) to setup CraftCMS content types, entry types and fields. Please do not make changes
in the CMS on dev or staging. Any changes should be made on your local dev and exported. See [Craft configuration](https://github.com/w3c/w3c-website-craft/blob/main/docs/craft_configuration.md)._

## Writing GraphQL

Once you know the fields you wish to retrieve you can start typing these in the GraphQL explorer and it will auto-complete
as you type. You can test your queries in the explorer before copying these to the Symfony project as GraphQL files.

Find out more on building GraphQL queries for [Matrix flexible component content fields](matrix-flexible-components.md).

## Docs

Some useful resources.

* [Intro to GraphQL](https://graphql.org/learn/)
* [CraftCMS GraphQL](https://craftcms.com/docs/3.x/graphql.html#), direct link to specific queries below:
  * [assets](https://craftcms.com/docs/3.x/graphql.html#the-assets-query)
  * [assetCount](https://craftcms.com/docs/3.x/graphql.html#the-assetcount-query)
  * [asset](https://craftcms.com/docs/3.x/graphql.html#the-asset-query)
  * [entries](https://craftcms.com/docs/3.x/graphql.html#the-entries-query)
  * [entryCount](https://craftcms.com/docs/3.x/graphql.html#the-entrycount-query)
  * [entry](https://craftcms.com/docs/3.x/graphql.html#the-entry-query) 
