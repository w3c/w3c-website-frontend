# Using the W3C Design System

HTML templates and global static assets (CSS/JS) are stored in the [W3C Design System](https://github.com/w3c/w3c-website-templates-bundle).

## Production

The Design System can be updated by merging changes to the `main` branch of [w3c-website-templates-bundle](https://github.com/w3c/w3c-website-templates-bundle)
and running `composer update` in this project (w3c-website-frontend).

Static assets are automatically uploaded to a CDN and you can choose where to point these to via the `ASSETS_WEBSITE_2021` setting in `.env.local`

For example:

```
# Production assets
ASSETS_WEBSITE_2021=https://www.w3.org/assets/website-2021/
```

You can SSH to the development or production servers via [SSH](../README.md#ssh-access).

## Testing development work

### Use the same branch names across the 2 repos

If you are making changes to the Design System and the W3C frontend website you need to make a branch for your work.
It is strongly recommended to use the same branch name on the `w3c-website-frontend` and `w3c-website-templates-bundle` repos.

### Create a Pull Request to test static assets

When a PR is created on the `w3c-website-templates-bundle` repo this pushes static assets to a CDN with a URL unique for your PR.
This allows you to test static assets before merging them into the `main` branch.

It's recommended you make this a draft PR until you are ready to get this reviewed.

When a PR is opened a GitHub action automatically posts a comment to the PR with the URL to the assets published to the CDN.

> Assets published to https://www-dev.w3.org/assets/website-2021-dev/pr-123/ and available via Composer package `w3c/website-templates-bundle:dev-feature/branch-name`

See:
- More details on [static assets](#static-assets).
- [Local testing](#local-testing) for how to test changes from a local version of the `w3c-website-templates-bundle` repo.

### HTML templates

HTML templates are loaded in the frontend app via Composer.

Find the version name to load in Composer via https://packagist.org/packages/w3c/website-templates-bundle

Update your `composer.json` to use this branch.

For example, for a branch called `feature/new` the Composer version you want to load is `dev-feature/new`

You can update your Composer file and the loaded package via:

```
composer require w3c/website-templates-bundle:dev-feature/new
```

This will automatically clear the cache, which helps pick up the new templates.

### Before you go live

The live website uses the `main` branch for static assets.

Make sure you switch back to this branch in Composer before merging your changes into the `main` branch of `w3c-website-frontend`:

```
composer require w3c/website-templates-bundle:dev-main
```

### Static assets

Static assets are delivered in the frontend app via a CDN URL.

Update the `ASSETS_WEBSITE_2021` setting in `.env.local` to point to the built static assets for this PR.

To test static assets pushed to the GitHub branch, you can use the custom CDN URL for the pull request:

```
ASSETS_WEBSITE_2021=https://www-dev.w3.org/assets/website-2021-dev/pr-123/
```

Replace `123` with your PR number. You can find this on the [w3c/website-templates-bundle branches page](https://github.com/w3c/w3c-website-templates-bundle/branches).

If you want to test if static assets have successfully uploaded to this PR URL you can test the URL: https://www-dev.w3.org/assets/website-2021-dev/pr-123/styles/core.css

Which should return the core CSS file. It will return an access denied message if the file does not exist.

### CMS

Finally, any CMS loaded content or assets is normally tested against the development CMS environment.
You can select which CMS environment your frontend app uses via `CRAFTCMS_API_URL` in your `.env.local` file.

```
# Development CMS
CRAFTCMS_API_URL="https://cms-dev.w3.org/api"
```

## Local testing

You can test HTML templates and static assets locally. The following instructions assume you are using DDEV
and have the `w3c-website-templates-bundle` repo cloned to `~/Sites/w3c/w3c-website-templates-bundle`

The file [docker-compose.mounts.yaml](.ddev/docker-compose.mounts.yaml) mounts the local w3c-website-templates-bundle directory into the frontend Docker container at `/home/w3c-website-templates-bundle`

You may need to run `ddev restart` to mount this folder.

### HTML templates

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

This will point the HTML templates at your local `w3c-website-templates-bundle` files.

> [!TIP]
> Please note this local repository configuration should only be used locally (don't commit this change to git) since it won't work on development or production.

To remove this "repositories" configuration run:

```shell
ddev composer config repositories.local --unset
ddev composer update
```

### Static assets

Create a symlink from `public/assets` to your local `w3c-website-templates-bundle` files.

```php
ddev ssh
ln -s /home/w3c-website-templates-bundle/public/dist/assets /var/www/html/public/assets
```

Update `.env.local`:

```dotenv
ASSETS_WEBSITE_2021=https://w3c-website-frontend.ddev.site/assets/
```

This will point the static assets at your local `w3c-website-templates-bundle` files.
