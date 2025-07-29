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
  * [Testing development work](#testing-development-work)
  * [Local testing](#local-testing)
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

### Production

The Design System can be updated by merging changes to the `main` branch of [w3c-website-templates-bundle](https://github.com/w3c/w3c-website-templates-bundle) 
and running `composer update` in this project (w3c-website-frontend).

Static assets are automatically uploaded to a CDN and you can choose where to point these to via the `ASSETS_WEBSITE_2021` setting in `.env.local` 

```
# Production assets
ASSETS_WEBSITE_2021=https://www.w3.org/assets/website-2021/
```

### Testing development work

#### Use the same branch names across the 2 repos

If you are making changes to the Design System and the W3C frontend website you need to make a branch for your work. 
It is strongly recommended to use the same branch name on the `w3c-website-frontend` and `w3c-website-templates-bundle` repos.

You also need to create a Pull Request on the `w3c-website-templates-bundle` repo for your new branch. 
It's recommended you make this a draft PR until you are ready to get this reviewed.

When you push files to your branch on `w3c-website-templates-bundle` static assets are automatically uploaded to a CDN with a URL unique to your PR ([see below](#static-assets)).

See [local testing](#local-testing) for how to test changes from a local version of the `w3c-website-templates-bundle` repo.

#### HTML templates

HTML templates are loaded in the frontend app via Composer.

Find the version name to load in Composer via https://packagist.org/packages/w3c/website-templates-bundle

Update your `composer.json` to use this branch.

For example, for a branch called `feature/new` the Composer version you want to load is `dev-feature/new`

You can update your Composer file and the loaded package via: 

```
composer require w3c/website-templates-bundle:dev-feature/new
```

This will automatically clear the cache, which helps pick up the new templates. 

#### Before you go live

The live website uses the `main` branch for static assets. 

Make sure you switch back to this branch in Composer before merging your changes into the `main` branch of `w3c-website-frontend`:

```
composer require w3c/website-templates-bundle:dev-main
```

#### Static assets

Static assets are delivered in the frontend app via a CDN URL.

Update the `ASSETS_WEBSITE_2021` setting in `.env.local` to point to the built static assets for this PR.

To test static assets pushed to the GitHub branch, you can use the custom CDN URL for the pull request:

```
ASSETS_WEBSITE_2021=https://www-dev.w3.org/assets/website-2021-dev/pr-123/
```

Replace `123` with your PR number. You can find this on the [w3c/website-templates-bundle branches page](https://github.com/w3c/w3c-website-templates-bundle/branches).

If you want to test if static assets have successfully uploaded to this PR URL you can test the URL: https://www-dev.w3.org/assets/website-2021-dev/pr-123/styles/core.css

Which should return the core CSS file. It will return an access denied message if the file does not exist.

#### CMS

Finally, any CMS loaded content or assets is normally tested against the development CMS environment. 
You can select which CMS environment your frontend app uses via `CRAFTCMS_API_URL` in your `.env.local` file. 

```
# Development CMS
CRAFTCMS_API_URL="https://cms-dev.w3.org/api"
```

### Local testing

You can test HTML templates and static assets locally. The following instructions assume you are using DDEV
and have the `w3c-website-templates-bundle` repo cloned to `~/Sites/w3c/w3c-website-templates-bundle`

The file [docker-compose.mounts.yaml](.ddev/docker-compose.mounts.yaml) mounts the local w3c-website-templates-bundle directory into the frontend Docker container at `/home/w3c-website-templates-bundle`

#### HTML templates

Add the local repository path to your `composer.json`:

```
ddev composer config repositories.local path "/home/w3c-website-templates-bundle/"
ddev composer update
```

This should add the following to your `composer.json` file:

```json
"repositories": {
  "local": {
    "type": "path",
    "url": "/home/w3c-website-templates-bundle/"
  }
}
```

You will need to remove this "repositories" configuration when you no longer want to use the local folder (e.g. if testing on development or when you go live).

You can do this via:

```shell
ddev composer config repositories.local --unset
ddev composer update
```

#### Static assets

Update `.env.local`:

```dotenv
ASSETS_WEBSITE_2021=http://localhost:8001/dist/assets/
```

This assumes you are running the Design System locally via:

```shell
php -S localhost:8000 -t _dist
```


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

Local front-end assets: 

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

If this fails to run you can manually clear cache files via:

```shell
rm -rf var/cache/*
```

## Built with

- [Symfony](https://symfony.com/)
- [Craft CMS API](https://craftcms.com/docs/4.x/graphql.html)
- [Strata Frontend](https://github.com/strata/frontend)
- [W3C Design System](https://github.com/w3c/w3c-website-templates-bundle/) (used as a Symfony template bundle)
