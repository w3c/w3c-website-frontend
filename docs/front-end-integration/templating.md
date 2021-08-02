# Twig templates

## Template bundle

We include the [W3C Symfony template bundle](https://github.com/w3c/w3c-website-templates-bundle) in this project. The 
aim is to store all global page and component templates in the Symfony bundle so they can be re-used on different 
projects.

On installation, front-end assets (CSS, JavaScript, fonts, etc.) will be copied into the public directory of the 
application. You will find them in the folder `public/bundles/w3cwebsitetemplates/dist/assets`

_TODO: How are front-end asset files updated on composer update?_

The Symfony [Asset](https://symfony.com/doc/current/components/asset.html) component is setup to load assets from the
`public/bundles/w3cwebsitetemplates/dist/assets` folder. This is setup in `config/packages/assets.yaml`

## Loading templates

Symfony templates are stored in `templates/`

To use templates from the bundle prefix with the handle `@W3CWebsiteTemplates`, for example:

```twig
{% extends '@W3CWebsiteTemplates/base.html.twig' %}
```

## Generating URLs

See https://symfony.com/doc/current/routing.html#generating-urls

TODO
