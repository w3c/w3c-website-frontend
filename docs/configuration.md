# Configuration

It's important to bear in mind this repository is public. Any sensitive variable needs to be excluded from code commits.

## Application parameters

Store any application parameters (e.g. default number of posts per page) under the `parameters` key in the 
`config/services.yaml` file. It's best practise to prefix parameters with `app.` to avoid any clashes.

E.g.

```yaml
parameters:
  app.posts_per_page = 20
```

These can be accessed in controllers via:

```
$postsPerPage = $this->getParameter('app.posts_per_page');
```

Also see [configuration parameters](https://symfony.com/doc/current/configuration.html#configuration-parameters).

## Environment variables

The following application environments are supported:
* `dev` (development - used to test functionality)
* `staging` (staging - used for user acceptance testing)
* `prod` (production)
* `test` (used for unit testing)

Store any environment variables that define infrastructure configuration (e.g. database DSN) to `.env` files. Symfony 
has a structured approach to loading env files, which is summarised below.

Also see [configuring .env files](https://symfony.com/doc/current/configuration.html#config-dot-env).

### .env config files safe to commit to git

* `.env` - environment variables for the application, shared across environments (or defaults to local development sedttings)
* `.env.dev` - environment variables for staging environment, do not include sensitive data
* `.env.staging` - environment variables for staging environment, do not include sensitive data
* `.env.prod` - environment variables for production environment, do not include sensitive data
* `.env.test` - environment variables for unit testing, do not include sensitive data
* `.env.local.dist` - template for required local environment variables (not used by Symfony, do not contain sensitive data)

###  .env config files which are local only, do not commit to git

* `.env.local` - local environment settings for the current environment (e.g. database DSN)

Please note any real environment variables override variables created with `.env` files.

### Accessing env variables

