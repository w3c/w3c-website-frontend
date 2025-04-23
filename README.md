# W3C frontend website

Symfony frontend website for w3.org

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
* https://www.w3.org
* https://www.w3.org/_build_summary.json (very brief summary of latest deployment)

### Staging
* https://www-staging.w3.org
* https://www-staging.w3.org/_build_summary.json

### Development
* https://www-dev.w3.org
* https://www-dev.w3.org/_build_summary.json

Used to test new functionality / changes. Access to development is restricted by IP address.

### Local
* http://localhost:8000/ (see [running application locally](#running-application-locally)) 

## Deployment

The project uses [Deployer](https://deployer.org/) to perform deployment.

Please note this project uses a local instance of Deployer (installed via Composer), as opposed to a global version of Deployer.

To run deployments please use:

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
w
These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

More on the [Git workflow for this project](docs/git_workflow.md).

### Requirements

* PHP 8.2+
* [Composer](https://getcomposer.org/)
* [Symfony CLI](https://symfony.com/download#step-1-install-symfony-cli)

### Clone the repository

`git clone git@github.com:w3c/website-frontend.git`

### Install Composer dependencies

```bash
composer install
```

***Note:*** If you already have this project installed locally and you're having trouble seeing any changes, make sure you have cleared your Symfony cache using the `bin/console cache:clear` command.

### Configuration

Create a local env file:

```
touch .env.local
```

And set:
* APP_ENV (dev, staging, prod)
* APP_URL
* CRAFTCMS_API_URL
* CRAFTCMS_API_READ_TOKEN
* CRAFTCMS_API_PUBLISH_TOKEN 

You can find your API Read and Publish tokens by going to the Craft CMS dashboard (see the [Craft repo](https://github.com/w3c/w3c-website-craft)).

You can check what env files are being loaded in your environment by running `php bin/console debug:dotenv`

### Running application locally

Before running the below command, please ensure you have the [Symfony CLI installed](https://symfony.com/download#step-1-install-symfony-cli)

Once you have the Symfony CLI installed (or you have ensured you already have it installed), you can run the frontend application locally at http://localhost:8000/ by running

```
symfony server:start
```
In your terminal

***Note:*** A good way to test if something will break if you deploy it to live is to switch your local env’s CraftCMS API URL and Read and Publish tokens to the production CMS
**(Please be careful about any changes you make in the Production CMS as they will be visible on the live site)**

## Built with

- [Symfony](https://symfony.com/)
- [Craft CMS API](https://craftcms.com/docs/4.x/graphql.html)
- [Strata Frontend](https://github.com/strata/frontend)
- [W3C Design System](https://github.com/w3c/w3c-website-templates-bundle/) (used as a Symfony template bundle)
