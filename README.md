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
* [Credits](#credits)

## Site URLs (TBC)

### Production
Live:
* https://www.w3.org
* https://www.w3.org/_build_summary.json (very brief summary of latest deployment)

Beta:
* https://beta.w3.org
* https://beta.w3.org/_build_summary.json

### Staging ***Not currently in use***
* https://www-staging.w3.org
* https://www-staging.w3.org/_build_summary.json

### Development
* https://www-dev.w3.org
* https://www-dev.w3.org/_build_summary.json

Access to https://www-dev.w3.org is restricted by IP.
**S24 Note: Connect to office VPN**

### Local
* http://localhost:8000/ (see [running application locally](#running-application-locally)) 

## Deployment (TBC)

The project uses [Deployer](https://deployer.org/) to perform deployment. For full deployment details/options view [docs/deployment](docs/deployment.md)

Please note this project uses a local instance of Deployer (installed via Composer), as opposed to a global version of Deployer. This is so we
can make use of other Composer packages in deployment tasks reliably (otherwise there can be clashes between global and local version of the same packages).

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
vendor/bin/dep deploy development --branch=develop
```

### SSH access
To connect to the server directly at the correct path for an environments current release, run the following from the root of the project

````
vendor/bin/dep ssh <environment>
````

## Installation

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

More on the [Git workflow for this project](docs/git_workflow.md).

### Requirements

* PHP 7.4 (Upgrading to 8.2)
* [Composer](https://getcomposer.org/)
* [Symfony CLI](https://symfony.com/download#step-1-install-symfony-cli)

### Clone the repository

`git clone git@github.com:w3c/website-frontend.git`

### Install and update composer dependencies

```bash
composer install

#Once the install is done

composer update
```

***Note:*** If you already have this project installed locally and you're having trouble seeing any changes, make sure you have cleared your Symfony cache using the `bin/console cache:clear` command.

### Configuration
Create a local environment file and populate the required variables (see `.env.local.dist`):

```
cp .env.local.dist .env.local
```
**Configure your local env file**
The W3C API URL and the Craft API URL are available in 1pass.
Ensure that your app environment is set to `dev`
You should be able to access your W3C API Key from your [W3C account](https://auth.w3.org/login)

### Craft CMS Tokens

You should be able to find your API Read and Publish tokens by going to the CraftCMS dashboard (see the [Craft repo](https://github.com/w3c/w3c-website-craft))

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
- [Strata Frontend](https://github.com/strata/frontend)
- [W3C Design System](https://github.com/w3c/w3c-website-templates-bundle/) (used as a Symfony template bundle)
