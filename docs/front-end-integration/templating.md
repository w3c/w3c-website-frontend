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

## Working on the frontend and templates bundle at the same time

If you need to make modifications to the templates bundle, it can be cumbersome to push changes and update dependencies
every time you make a change.

A simple workaround is to create a symlink to the bundle in the frontend's vendor directory.
To do so, run the following command (replace `${BASE}` with the path to your local checkout of the templates bundle):
```shell
composer install # download dependencies
rm -rf vendor/w3c/website-templates-bundle # delete the template bundle's folder
ln -s ${BASE}/w3c-website-templates-bundle vendor/w3c/website-templates-bundle # create the symlink to the bundle
```

For example if your template bundle is stored in `~/Sites/w3c/w3c-website-templates-bundle` the symlink command is:

```shell
ln -s ~/Sites/w3c/w3c-website-templates-bundle vendor/w3c/website-templates-bundle
```

That will replace the folder `vendor/w3c/website-templates-bundle` with a symlink to your development version of the
bundle.

You can now make changes to the templates bundle and test them directly in the frontend.

Then, when you finally push your changes to the templates bundle, you'll need to synchronize `composer.lock` with the
new version for `composer install` to download the new version. To do so, run the following command (replace `${BASE}` with the path to your local checkout of the
templates bundle):
```shell
rm -rf vendor/w3c/website-templates-bundle # delete the symlink
composer update # update dependencies
rm -rf vendor/w3c/website-templates-bundle # delete the folder
ln -s ${BASE}/w3c-website-templates-bundle vendor/w3c/website-templates-bundle # re-create the symlink to the bundle
```
These commands can be chained using `&&`.
