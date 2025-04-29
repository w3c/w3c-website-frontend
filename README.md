# W3C frontend website

Frontend website for the main W3C website at w3.org, built in Symfony.

## Reporting issues

Please report any issues to the [w3c-website](https://github.com/w3c/w3c-website/issues) repo.

## Getting started

This document is a summary of what you need to know when working on this project. Please also read the more [detailed project documentation](docs/README.md)

### In this document

* [Site URLs](#site-urls)
* [Deployment](#deployment)
* [Installation](#installation)
* [Built with](#built-with)

## Site URLs

### Production
Live:
* https://www.w3.org (summary of [latest deployment](https://www.w3.org/_build_summary.json))

### Development
* https://www-dev.w3.org (summary of [latest deployment](https://www-dev.w3.org/_build_summary.json))

Used to test new functionality / changes. Access to development is restricted by IP address.

### Local
* http://localhost:8000/ (Local PHP)
* https://w3c-website-frontend.ddev.site (DDEV)

## Deployment

The project uses [Deployer](https://deployer.org/) to publish updates to the websites.

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
To connect to the server directly at the correct path for an environments current release, run the following from the root of the project

````
vendor/bin/dep ssh <environment>
````

## Updating HTML templates

The HTML templates are stored in [w3c-website-templates-bundle](https://github.com/w3c/w3c-website-templates-bundle)

These can be updated by deploying changes to the [design system](https://github.com/w3c/w3c-website-templates-bundle/blob/main/design-system.md) 
and running `composer update` in this project.

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

To use DDEV as your local environment:

```shell
ddev start
ddev composer install
```

[Create an .env.local config file](#configuration) as described below.

To access the website on https://w3c-website-frontend.ddev.site

```shell
ddev launch
```

To access other local projects from within a DDEV container, for example the CMS API, use local DDEV URLs: 

* CMS API: https://ddev-w3c-website-craft-web/api
* Frontend: https://ddev-w3c-website-frontend-web

### Configuration

In Symfony the `.env.local` file contains local overrides for `.env`. 

Create a local env file:

```
touch .env.local
```

And set:

```dotenv
# Application environment (dev, staging, prod)
APP_ENV=dev
APP_URL=https://w3c-website-frontend.ddev.site

# Craft API
CRAFTCMS_API_URL="https://ddev-w3c-website-craft-web/api"
CRAFTCMS_API_READ_TOKEN=""
CRAFTCMS_API_PUBLISH_TOKEN=""
CRAFTCMS_API_COOKIE_VALUE=""

# Point assets to W3C CDN for local dev
ASSETS_WEBSITE_2021=https://www.w3.org/assets/website-2021/
```

Set the Craft API URL to the Craft instance you want to read in content for your local development site.
You can set this to production if you want to test how the local dev site will work with live content (we recommend only setting the API READ token on production).

You can find your API Read and Publish tokens by going to the Craft CMS dashboard (see the [Craft repo](https://github.com/w3c/w3c-website-craft)).

You can check what env files are being loaded in your environment by running:

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
