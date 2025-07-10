# W3C frontend website

Frontend website for the main W3C website at w3.org, built in Symfony.

## Reporting issues

Please report any issues to the [w3c-website](https://github.com/w3c/w3c-website/issues) repo.

## Getting started

This document is a summary of what you need to know when working on this project. Please also read the more [detailed project documentation](docs/README.md)

### In this document

* [Site URLs](#site-urls)
* [Deployment](#deployment)
* [Related W3C repos](#related-w3c-repos)
* [Updating HTML templates](#updating-html-templates)
* [Installation](#installation)
* [Built with](#built-with)

## Site URLs

### Production
* https://www.w3.org

### Development
* https://www-dev.w3.org

Used to test new functionality / changes. Access to development is restricted by IP address.

### Local
* http://localhost:8000/ (via Symfony CLI)
* https://w3c-website-frontend.ddev.site (via DDEV)

## Deployment

The project uses [Deployer](https://deployer.org/) to publish updates to the websites.

The following environments are setup:
* production
* development

To run a deployment please use:

````
vendor/bin/dep deploy <environment>
````

To deploy a specific branch use

````
vendor/bin/dep deploy <environment> --branch=<branch_name>
````

E.g.

```
vendor/bin/dep deploy development --branch=feature/my-branch-name
```

### SSH access
To connect to the server directly at the correct path for an environment's current release, run the following from the root of the project

````
vendor/bin/dep ssh <environment>
````

## Related W3C repos

* [w3c/w3c-website-craft](https://github.com/w3c/w3c-website-craft) - Craft CMS installation (private repo)
* [w3c/w3c-website-templates-bundle](https://github.com/w3c/w3c-website-templates-bundle) - Front-end templates

## Updating HTML templates

The HTML templates are stored in [w3c-website-templates-bundle](https://github.com/w3c/w3c-website-templates-bundle)

These can be updated by deploying changes to the [design system](https://github.com/w3c/w3c-website-templates-bundle/blob/main/design-system.md) 
and running `composer update` in this project (w3c-website-frontend).

You can also test changes either by deploying a branch to the staging environment for the design system, or by [testing a development branch on the frontend website](https://github.com/w3c/w3c-website-templates-bundle/blob/main/design-system.md#testing-a-development-branch-on-your-front-end-website). 

## Installation
These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

More on the [Git workflow for this project](docs/git_workflow.md).

### Requirements

* [DDEV](https://ddev.readthedocs.io/en/stable/) 

or:

* PHP 8.2+
* [Composer](https://getcomposer.org/)
* [Symfony CLI](https://symfony.com/download#step-1-install-symfony-cli)

### Clone the repository

First clone the git repo to your local filesystem:

```bash
git clone git@github.com:w3c/w3c-website-frontend.git
```

### Local PHP

If you run PHP locally: 

```shell
composer install
```

[Create an .env.local config file](#configuration) as described below.

You can run the frontend application locally at http://localhost:8000/ by running:

```
symfony server:start
```

### DDEV

To use DDEV:

```shell
ddev start
ddev composer install
```

[Create an .env.local config file](#configuration) as described below.

You can launch the website on https://w3c-website-frontend.ddev.site via:

```shell
ddev launch
```

To access other local projects from within a DDEV container, for example the CMS API, use local DDEV URLs: 

* CMS API: https://ddev-w3c-website-craft-web/api
* Frontend: https://ddev-w3c-website-frontend-web

### Configuration

#### .env.local

In Symfony the `.env.local` file contains local overrides for `.env` and is not committed to source control.

Create a local env file:

```
touch .env.local
```

Studio 24 staff can create a copy of `.env.local.dist` and populate it with shared secrets from [1Password CLI](https://developer.1password.com/docs/cli/get-started#install):

```
op inject -i .env.local.dist -o .env.local
```

#### Environment variables

And set the required environment variables. The example below is the recommended settings for DDEV, update these if you are using local PHP.

```dotenv
# Application environment (dev, staging, prod)
APP_ENV=dev
APP_URL=https://w3c-website-frontend.ddev.site

# W3C API API key
# see https://w3c.github.io/w3c-api/
W3C_API_KEY=""

# Craft API
CRAFTCMS_API_URL="https://cms.w3.org/api"
CRAFTCMS_API_READ_TOKEN=""
CRAFTCMS_API_PUBLISH_TOKEN=""

# W3C design system
ASSETS_WEBSITE_2021=https://www.w3.org/assets/website-2021/
```

#### W3C API

Set the W3C API key

#### Craft API 

Set the Craft API URL to the Craft instance you want to read in content for your local development site.
You can set this to production if you want to test how the local dev site will work with live content (we recommend only setting the API_READ token when using the production API).

Use production CMS:

```
CRAFTCMS_API_URL="https://cms.w3.org/api"
```

Use development CMS:

```
CRAFTCMS_API_URL="https://cms-dev.w3.org/api"
```

Use local CMS:

```
CRAFTCMS_API_URL="https://ddev-w3c-website-craft-web/api"
```

You can find your API Read and Publish tokens by going to the Craft CMS dashboard (see the [Craft repo](https://github.com/w3c/w3c-website-craft)).

#### Website assets

The website assets are now loaded from a CDN, we recommend using production assets unless you are working on front-end changes.

Use production assets:

```
ASSETS_WEBSITE_2021=https://www.w3.org/assets/website-2021/
```

Use development assets:

```
ASSETS_WEBSITE_2021=https://dev.w3.org/assets/website-2021/
```

If you are making changes to the front-end assets, you'll need to point to a local location. 

Use local assets:

```
ASSETS_WEBSITE_2021=https://ddev-w3c-website-frontend-web/assets/website-2021/
```

#### Testing

You can check what environment variables are being loaded by running:

```shell
php bin/console debug:dotenv

## DDEV
ddev console debug:dotenv
```

### Troubleshooting

If you already have this project installed locally and you're having trouble seeing any changes, make sure you have cleared your Symfony cache:

```shell
bin/console cache:clear

## DDEV
ddev console cache:clear
```

## Built with

- [Symfony](https://symfony.com/)
- [Craft CMS API](https://craftcms.com/docs/4.x/graphql.html)
- [Strata Frontend](https://github.com/strata/frontend)
- [W3C Design System](https://github.com/w3c/w3c-website-templates-bundle/) (used as a Symfony template bundle)
