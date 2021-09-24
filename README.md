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

### Staging
* https://www-staging.w3.org
* https://www-staging.w3.org/_build_summary.json

### Development
* https://www-dev.w3.org
* https://www-dev.w3.org/_build_summary.json

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

You will need to obtain from another developer: *[to update later as sharing process put in place]*
* A copy of a local `.env.local` file

Clone the repository

`git clone git@github.com:w3c/website-frontend.git`

Run `composer install`

Create a local environment file and populate the required variables (see `.env.local.dist`).

```angular2html
cp .env.local.dist .env.local
```

More on the [Git workflow for this project](docs/git_workflow.md).

### Running application locally

You can run the frontend application locally at http://localhost:8000/ by running the command:

```
symfony server:start
```

## Built with

- [Symfony](https://symfony.com/)
- [Strata Frontend](https://github.com/strata/frontend)
- [W3C Design System](https://github.com/w3c/w3c-website-templates-bundle/) (used as a Symfony template bundle)
