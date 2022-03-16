# Caching

## Caching strategy for W3C website

We use Cloudflare for full-page caching of web pages whose content is the same for all users. The 
new W3C site uses JavaScript to update the user menu client-side to help enable normal pages to be cached.

Our strategy is:

* All public content pages are cached via Cloudflare to aid performance (full-page caching)
* Any personalised content (e.g. user account menu) is requested over JavaScript (XMLHttpRequest)
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

It's simple enough to identify the homepage content by its URL.

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

You can apply this tag from a query object via:

```php
$query->cacheTagGlobal();
```

### sections

Any changes to any content within a section. Sections can have any name. For content in Craft CMS we recommend 
using the `sectionHandle` string from Craft CMS, for example, `blogPosts` for the blog posts.

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

This results in the `new-` prefix to the cache tag.

## Marking pages as cacheable

We are currently using configuration to setup caching response headers in the Symfony app. 

See [caching headers](https://foshttpcachebundle.readthedocs.io/en/latest/features/headers.html) in FOSHttpCacheBundle 
and the configuratio file [fos_http_cache.yaml](../config/packages/fos_http_cache.yaml).

To set cache headers requires adding the cache tags to query objects (see above for example code).

## Adding cache tags to the response

To automatically output cache tags from query objects to your page response add the following event subscriber in your `services.yaml` file:

```yaml
   strata.event_subscriber.response_helper:
      class: Strata\SymfonyBundle\EventSubscriber\ResponseHelperEventSubscriber
      arguments:
        $responseTagger: '@fos_http_cache.http.symfony_response_tagger'
        $manager: '@strata.query_manager'

```

## Marking pages as not cacheable

Update the [fos_http_cache.yaml](../config/packages/fos_http_cache.yaml) configuration and set the cache_control to:

```
cache_control: { private: true, no_cache: true, no_store: true }
```

## Clearing the cache

### Console application

_TODO: work-in-progress._

This clears the Cloudflare and local data cache by:

* Clear entire cache
* Clear cache by cache-tag
* Clear cache by URL

### Craft CMS

_TODO: work-in-progress._

We have a CraftCMS plugin that clears the cache when content is updated. This also includes the facility to manually 
clear the entire cache (purge all files) and purge by URL.

Functionality:

* Send clear cache request to frontend on page update (published only, not draft)
* Manually clear the entire cache
* Manually clear cache by URL (only works for the frontend URL)
* Manually clear cache by cache tag

### FOSHttpCache

To clear the Cloudflare cache you can use [FOSHttpCache](https://foshttpcache.readthedocs.io/):

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
