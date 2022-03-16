# Caching

## Caching strategy for W3C website

We use Cloudflare for full-page caching of web pages whose content is the same for all users. The 
new W3C site uses JavaScript to update the user menu client-side to help enable normal pages to be cached.

Our strategy is:

* All public content pages are cached via Cloudflare to aid performance (full-page caching)
* Any restricted access pages (i.e. behind user login) must not be cached
* Only use data caching for data that is shared across multiple pages (data associated with an individual page is more efficiently cached via full-page caching) 
* When content is changed in Craft CMS (or elsewhere) the cache is cleared (invalidated) to ensure fresh content is served to users

## Invalidating the cache for data components

One of the challenges of clearing the cache is how to track all the different data components that might cause a given 
page to be invalidated. 

For example, the W3C homepage currently contains the following data components:

* Homepage page content (from Craft CMS)
* Navigation (from Craft CMS)
* Recent activity: across blog posts, news articles, press releases (from Craft CMS)
* List of W3C members (from W3C API)

It's simple enough to identify the homepage content by its entry ID in the CMS.

We use cache tags to help identify other data components. For example:

* Navigation: `global`
* Recent activity: `new-blogPosts`, `new-newsArticles`, `new-pressReleases`
* List of W3C members: `members`

The cache tags are explained below...

### global

Any data that is displayed on all, or most, pages can use the `global` cache tag. 

Any changes to content marked as global will clear the cache for all pages using this cache tag.
It's recommended you take care when using the 
`global` cache tag since it's likely to purge the majority of pages in the cache.

You can apply this tag from a query via:

```php
$query->cacheTagGlobal();
```

### sections

Any changes to any content within a section. Sections can have any name, for example, `blogPosts` for the blog posts 
section from the CMS (this uses the `sectionHandle` string from Craft CMS).

Any changes to content within this section will purge this cache tag. You should use this cache tag sparingly and only 
when it's really important to update content (since it could purge the cache for lots of pages). 

```php
// One tag
$query->cacheTag('blogPosts');

// Multiple tags
$query->cacheTags(['blogPosts', 'newsPosts']);
```

### new content in sections

To make it easier to conditionally clear the cache the special `new-[section]` cache tag exists. 

This purges the tag only when new entries are added to a section or if any changes happen to the 10 most recent items in a section. This helps reduce 
cache invalidation when it may not be required.

```php
// One tag
$query->cacheTagNew('blogPosts');

// Multiple tags
$query->cacheTagsNew('blogPosts', 'newsPosts');
```

## Marking pages as cacheable

See [caching headers](https://foshttpcachebundle.readthedocs.io/en/latest/features/headers.html) in FOSHttpCacheBundle.

## Marking a page with cache tags

To output cache tags from queries to your page response add the following event subscriber in your `services.yaml` file:

```yaml
   strata.event_subscriber.response_helper:
      class: Strata\SymfonyBundle\EventSubscriber\ResponseHelperEventSubscriber
      arguments:
        $responseTagger: '@fos_http_cache.http.symfony_response_tagger'
        $manager: '@strata.query_manager'

```

## Marking pages as not cacheable

TODO

To mark a page as this must not be cached:

```php
// @todo
something->doNotCache();
```

## Clearing the cache

### Craft CMS

We have a CraftCMS plugin that clears the cache when content is updated. This also includes the facility to manually 
clear the entire cache (purge all files) and purge by URL.

Functionality:

* Send clear cache request to frontend on page update (published only, not draft)
* Manually clear the entire cache
* Manually clear cache by URL (only works for the frontend URL)
* Manually clear cache by cache tag

### Frontend application

You can also clear the cache programmatically in a number of ways.

To clear the data cache in the Symfony frontend application and in Cloudflare, you can send 
API requests to the front-end app. This can clear the cache by:

* Clear all cache
* Clear by tag
* Clear by URL (this retrieves cache tags from the URL, then clears by cache tag)

TODO

### FOSHttpCache

To only clear the Cloudflare cache you can use [FOSHttpCache](https://foshttpcache.readthedocs.io/):

```php
use FOS\HttpCache\CacheInvalidator;
use FOS\HttpCache\ProxyClient\Cloudflare;
use FOS\HttpCache\ProxyClient\HttpDispatcher;

$options = [
    'authentication_token' => '<user-authentication-token>',
    'zone_identifier' => '<my-zone-identifier>',
];

$httpDispatcher = new HttpDispatcher(['https://api.cloudflare.com']);
$cloudflare = new Cloudflare($httpDispatcher, $options);
$cacheInvalidator = new CacheInvalidator($cloudflare);
```

You can then invalidate by URL:

```php
$cacheInvalidator->invalidatePath('https://www.w3.org/url')->flush();
```

Or by cache tag:

```php
$cacheInvalidator->invalidateTags(['tag-one', 'tag-two'])->flush();
```

See FOSHttpCache [Cloudflare client docs](https://foshttpcache.readthedocs.io/en/latest/proxy-clients.html#cloudflare-client).

## Frontend API

* Ensure `/strata-api/` not full-page cached

```
POST frontend-url/strata-api/clear-cache?url={token}&token={token}
```

How this works:

* Authenticate request
* Clear cache by tag, if by URL request page to get tags
* Invalidate cache by URL and tags (Cloudflare & local Symfony data cache)
* Return success code

By default, exclude 'global' cache from page-level cache clearing.

## Cache tags

>  For me the biggest challenge seems to be how to track all the data components that might cause a given page to be invalidated

Strategy:
* Clear URL
* How about other resources linked, e.g. listing page for a blog page? How do we track these?
* What about Resource tags? (then set cache-tags)

E.g.

```
// type, ID
$response->setResourceTag('blog', 534);

```

