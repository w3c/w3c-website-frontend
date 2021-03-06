# Adding languages

We aim to use standard locale strings, based on the [ISO 639-1 alpha-2 language list](https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes) 
and any required script and region strings. Since locales are used in URLs, we use `-` instead of `_` and lower-case for clean URLs.

For example French is `fr`, Portuguese - Brazil is `pt-br`

Below we'll use the character `{lc}` to refer to the locale code.

## Craft CMS

You need to setup a site in Craft with the correct locale language. Make a note of the site ID since we need to set 
this in the frontend app.

Please refer to the [instructions for adding a new language in Craft in the W3C website Craft repo](https://github.com/w3c/w3c-website-craft/blob/main/docs/new_language.md) 

## Routing

Add the language URL prefix to [config/routes/annotations.yml](../config/routes/annotations.yaml) in 
`controllers.prefix` in the format:

```yaml
{lc}: '/{lc}'
```

E.g. for French:

```yaml
fr: '/fr'
```

See [Internationalized routing](https://symfony.com/blog/new-in-symfony-4-1-internationalized-routing).

## Hosting platform routing

Please note only fixed prefixes are routed from w3.org to the Symfony app. To ensure the new language route points to 
the Symfony frontend web app ...

TODO - W3C

## Frontend site setup

Edit the `src/Service/SiteConfigurator.php` file and add the locale along with the CraftCMS site handle. This helps auto-set the
correct site handle when retrieving content. It also helps set whether content is left-to-right (default) or right-to-left.

In the format:

```php
$site->addLocale('{lc}', ['siteHandle' => {handle}]);
```

For example, to add German:

```php
$site->addLocale('de', ['siteHandle' => 'german']);
```

You can use `$site->addRtfLocale()` to add a right-to-left locale. For example, to add Arabic:

```php
$site->addRtfLocale('ar', ['siteHandle' => 'arabic']);
```

## Translation messages file

Add a file named `translations/messages+intl-icu.{lc}.yaml`, copy and paste the contents of the English (`en`) file as the starting point.

Do the same in the [template bundle codebase](https://github.com/w3c/w3c-website-templates-bundle). In the translations folder, add a file named 
`w3c_website_templates_bundle+intl-icu.{lc}.yaml`. Copy and past the contents of the English yaml file and replace the entry values with translations in the 
target language.

Please note if a messages file does not exist the application defaults to English.  

See [translation messages](internationalization.md#messages).
