# W3C frontend website

Frontend website for the main W3C website at w3.org, built in Symfony.

Also see:
* [w3c/w3c-website-craft](https://github.com/w3c/w3c-website-craft) - Craft CMS (private repo)
* [w3c/w3c-website-templates-bundle](https://github.com/w3c/w3c-website-templates-bundle) - W3C Design system (front-end templates)

## Reporting issues

Please report any issues to the [w3c-website](https://github.com/w3c/w3c-website/issues) repo.

## Getting started

This document is a summary of what you need to know when working on this project. Please also read the more [detailed project documentation](docs/README.md)

### In this document

* [Site URLs](#site-urls)
* [SSH access](#ssh-access)
* [Deployment](#deployment)
* [Using the W3C Design System](docs/using-the-design-sytem.md)
* [Installation](#installation)
* [Built with](#built-with)

## Site URLs

Please note, W3C has a `development` environment, not staging.

### Production
* https://www.w3.org

### Development
* https://www-dev.w3.org

Used to test new functionality / changes. Access to development is restricted by IP address.

### Local
* http://localhost:8000/ (via Symfony CLI)
* https://w3c-website-frontend.ddev.site (via DDEV)

## SSH access
To connect to the server directly at the correct path for the current release, run the following from the root of the project

````
ddev dep ssh <environment>
````

You can also check what was last deployed:

````
ddev dep show <environment>
````

## Deployment

The project uses [Deployer](https://deployer.org/) to publish updates to the websites.

To run a deployment please use:

````
./vendor/bin/dep deploy <environment>

# DDEV
ddev dep deploy <environment>
````

To deploy a specific branch use

````
./vendor/bin/dep deploy <environment> --branch=<branch_name>

# DDEV
ddev dep deploy <environment> --branch=<branch_name>
````

## Using the W3C Design System

See [Using the W3C Design System](docs/using-the-design-sytem.md).

## Installation
These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

More on the [Git workflow for this project](docs/git_workflow.md).

### Requirements

* [DDEV](https://ddev.readthedocs.io/en/stable/)

or:

* PHP 8.2+
* [Composer](https://getcomposer.org/)
* [Symfony CLI](https://symfony.com/download#step-1-install-symfony-cli)

### Note on SSH setup

In order to deploy to the W3C hosting environment you need to update your local ssh config (`.ssh/config`) with the following code:

````
Host *.w3.internal
ProxyJump studio24@ssh-aws.w3.org
````

If you deploy via DDEV this is automatically set up for you.

You can test this works by:

```
ddev dep ssh development
```

The W3C team also need to ensure your SSH key is set up for the `studio24` user.

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
```

Create your `.env.local` config file, see [configuration](#configuration), and then run:

```shell
ddev composer install
```

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

Production CMS:

```
CRAFTCMS_API_URL="https://cms.w3.org/api"
```

Development CMS:

```
CRAFTCMS_API_URL="https://cms-dev.w3.org/api"
```

Local CMS:

```
CRAFTCMS_API_URL="https://ddev-w3c-website-craft-web/api"
```

You can find your API Read and Publish tokens by going to the Craft CMS dashboard (see the [Craft repo](https://github.com/w3c/w3c-website-craft)).

#### Website assets

The website assets are now loaded from a CDN, we recommend using production assets unless you are working on front-end changes.

Production assets:

```
ASSETS_WEBSITE_2021=https://www.w3.org/assets/website-2021/
```

Testing assets via a Pull Request:

```
ASSETS_WEBSITE_2021=https://www-dev.w3.org/assets/website-2021-dev/pr-123/
```

See [testing development work](#testing-development-work) for instructions on how to test a branch in the design system on the frontend website.

Local front-end assets: 

```
ASSETS_WEBSITE_2021=https://w3c-website-frontend.ddev.site/dist/assets/
```

See [local testing](#local-testing) for more information on how to test changes to the design system locally.

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

If this fails to run you can manually clear cache files via:

```shell
rm -rf var/cache/*
```

## Built with

- [Symfony](https://symfony.com/)
- [Craft CMS API](https://craftcms.com/docs/4.x/graphql.html)
- [Strata Frontend](https://github.com/strata/frontend)
- [W3C Design System](https://github.com/w3c/w3c-website-templates-bundle/) (used as a Symfony template bundle)
