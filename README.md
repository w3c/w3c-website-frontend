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
* [Using the W3C Design System](#using-the-w3c-design-system)
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
./vendor/bin/dep deploy <environment>
````

To deploy a specific branch use

````
./vendor/bin/dep deploy <environment> --branch=<branch_name>
````

E.g.

```
./vendor/bin/dep deploy development --branch=feature/my-branch-name
```

### SSH access
To connect to the server directly at the correct path for an environment's current release, run the following from the root of the project

````
./vendor/bin/dep ssh <environment>
````

## Related W3C repos

* [w3c/w3c-website-craft](https://github.com/w3c/w3c-website-craft) - Craft CMS installation (private repo)
* [w3c/w3c-website-templates-bundle](https://github.com/w3c/w3c-website-templates-bundle) - Front-end templates

## Using the W3C Design System

HTML templates and global static assets (CSS/JS) are stored in the [W3C Design System](https://github.com/w3c/w3c-website-templates-bundle). 

The Design System can be updated by merging changes to the `main` branch of [w3c-website-templates-bundle](https://github.com/w3c/w3c-website-templates-bundle) 
and running `composer update` in this project (w3c-website-frontend).

### Testing development work

You can test changes before they are made live by creating a Pull Request from a branch on the [w3c-website-templates-bundle](https://github.com/w3c/w3c-website-templates-bundle) repo.

To use this in the frontend repo you need to load the HTML template via Composer and point to the built static assets via an environment variable.

#### HTML templates

Find the branch name to load in Composer via https://packagist.org/packages/w3c/website-templates-bundle

Update your `composer.json` to use this branch.

For example, for a branch called `feature/new` the composer.json will look like:

```
    "w3c/website-templates-bundle": "dev-feature/new"
```

Run `ddev composer update` to update the files loaded by Composer.
 
Run `ddev console cache:clear` to clear your local Symfony cache.

Make sure you switch back to the production setting in Composer before making your changes to the frontend repo live:

```
    "w3c/website-templates-bundle": "dev-main"
```

#### Static assets

Update the `ASSETS_WEBSITE_2021` setting in `.env.local` to point to the built static assets for this PR.

GitHub actions will create a custom assets folder based on the PR number which you can use in the frontend:

```
ASSETS_WEBSITE_2021=https://www-dev.w3.org/assets/website-2021-dev/pr-123/
```

Replace `pr-123` with your PR number.

## Installation
These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

More on the [Git workflow for this project](docs/git_workflow.md).

### SSH setup
To deploy the website you need to add the following to your `~/.ssh/config` file:

```
Host *.w3.internal
ProxyJump studio24@ssh-aws.w3.org
```

You can test this works by:

```
./vendor/bin/dep ssh development
```

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

To access the template bundle locally, use the localhost URL:

* Template bundle: http://localhost:8001/

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

You can test frontend changes using your local front-end assets: 

```
ASSETS_WEBSITE_2021=http://localhost:8001/dist/assets/
```

See [testing development work](#testing-development-work) for instructions on how to test a branch in the design system on the frontend website.

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
