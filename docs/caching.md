# Caching

## Caching strategy for W3C website

We use Cloudflare for full-page caching of web pages whose content is the same for all users.

Our strategy is:

* All public content pages are cached via Cloudflare to aid performance (full-page caching)
* Any personalised content (e.g. user account menu) is requested over JavaScript (XMLHttpRequest) and not cached
* Any restricted access pages (i.e. behind user login) must not be cached
* Only use data caching for data that is shared across multiple pages (data associated with an individual page is more efficiently cached via full-page caching)
* When content is changed in Craft CMS (or elsewhere) the cache is cleared (invalidated) to ensure fresh content is served to users

## Marking pages as cacheable

The basic requirement is to send a `cache-control` header to tell browsers and Cloudflare to cache page content. Example
response headers:

Cache for a day:

```
cache-control: max-age=86400, public
```

### Set caching headers via FOSHttpCache

We are currently using configuration to setup caching response headers in the Symfony app.

See [caching headers](https://foshttpcachebundle.readthedocs.io/en/latest/features/headers.html) in FOSHttpCacheBundle
and the configuration file [fos_http_cache.yaml](../config/packages/fos_http_cache.yaml)

### Setting headers in Symfony

You can also output headers directly in Symfony:

```php
$response->headers->set('Cache-Control', ' max-age=86400, public');
```

### Adding cache tags to the response

To help clear cache for related content on a page we use cache tags. Cache tags are set on query objects in the front-end application.

To automatically output cache tags from query objects to your HTTP responses set `strata.tags.enabled` to true in [config/packages/strata.yaml](../config/packages/strata.yaml)

## Marking pages as not cacheable

An example response header to mark a page as not cacheable:

```
cache-control: no-cache, private
```

### Set caching headers via FOSHttpCache

Update the [fos_http_cache.yaml](../config/packages/fos_http_cache.yaml) configuration and set the `cache_control` setting to:

```
cache_control: { no_cache: true }
```

### Setting headers in Symfony

You can also output headers directly in Symfony:

```php
$response->headers->set('Cache-Control', 'no-cache, private');
```

## Using cache tags to invalidate the cache for data components

One of the challenges of clearing the cache is how to track all the different data components that might cause a given
page to be invalidated.

For example, as of March 2022, the W3C homepage currently contains the following data components:

* Homepage page content (from Craft CMS)
* Navigation (from Craft CMS)
* Recent activity: across blog posts, news articles, press releases (from Craft CMS)
* List of W3C members (from W3C API)

We can easily clear the cache for the homepage page content via it's URL. By setting cache tags we can also
invalidate the homepage cache if any other content is updated (e.g. new blog post).

We use cache tags to help identify other data components. For example:

* Page content: `homepage`
* Navigation: `mainNavigation`
* Recent activity: `new-blogPosts`, `new-newsArticles`, `new-pressReleases`
* List of W3C members: `members`

The cache tags are explained below...

### Sections

Any changes to any content within a section. Sections can have any name. For content in Craft CMS we recommend
using the `sectionHandle` string from Craft CMS, for example, `blogPosts` for the blog posts. This makes it easy for us
to clear the cache based on section handles in Craft CMS when content changes.

Any changes to content within this section will purge this cache tag. You should use this cache tag sparingly and only
when it's really important to update content since it could purge the cache for lots of pages.

For example, clearing the cache for `blogPosts` would clear the homepage, blog listing pages and all blog post pages.

```php
// One tag
$query->cacheTag('blogPosts');

// Multiple tags
$query->cacheTags(['blogPosts', 'newsPosts']);
```

### New content in sections

To make it easier to conditionally clear the cache the special `new-[section]` cache tag exists.

This purges the tag only when new entries are added to a section or if any changes happen to the most recent items in a section. This helps reduce
cache invalidation when it may not be required.

For example, clearing the cache for `new-blogPosts` would only clear the cache for pages that use this cache tag if a
new blog post is added or any changes are made to the 10 most recent blog posts.

You can manually add tags with the `new-` prefix or you can use the helper methods below to automatically add the prefix:

```php
// One tag
$query->cacheTagNew('blogPosts');

// Multiple tags
$query->cacheTagsNew('blogPosts', 'newsPosts');
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

To clear the Cloudflare cache you can use [FOSHttpCache](https://foshttpcache.readthedocs.io/) in your PHP application.
Example usage:

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
