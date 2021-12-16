# Caching

Uses [FOSHttpCacheBundle](https://foshttpcachebundle.readthedocs.io/)

* Ensure `/strata-api/` not full-page cached

```
POST frontend-url/strata-api/clear-cache?url={token}&token={token}
```

How this works:

* Authenticate request
* If page in local site cache, read cache tags
* Forward request to frontend URL to generate page content & generate cache tags (ResponseTagger) https://symfony.com/doc/current/controller/forwarding.html
* When response returned, do not return page but process API request (EventListener)
* Save cache tags to local site cache
* Invalidate cache by URL and tags (Cloudflare & local Symfony data cache)
* Return success code

By default, exclude 'global' cache from page-level cache clearing.

## Craft CMS plugin

Functionality:

* Send clear cache request to frontend on page update (published only, not draft)
* Admin page to clear cache by URL (same as above, though manual)
* Admin page to clear global cache (cache tag: global)

HTTP request:

* Send request to URL to cache clear
* Add GET param to exclude from Cloudflare cache
* Use auth token to authenticate requests 


