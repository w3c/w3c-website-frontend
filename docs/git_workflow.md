# Git workflow

The branch called `main` is the production branch, i.e. it always corresponds to the live website CMS.

You should always perform development work in another branch and merge your working branch into `main` when ready. To do so,
you will need to issue a pull request. The pull request will require approval by another developer and passing [continuous integration](continuous_integration.md) tests before merging.

## Naming branches

Here are our recommendations for naming branches **in the context of this project**.

If you are working on a new feature, please name your branch `feature/<descritptive-name>`, e.g. `feature/super-custom-field-module`

If several developers are working on various features are the same time, and their work is interdependent, it is worth creating a `develop` branch from which to work. You should work directly in develop
or merge your working branches in `develop` frequently.

If several developers are working on various features at the same time, and in theory their work should be independent but tested and released at the same time, it is worth creating a 'release'
branch in which to merge all the working branches for testing and release. Please name release branches `release/<descriptive-name>`, e.g. `release/january-website-upgrade`.

If you are working on an urgent fix to the production site, please create a 'hotfix' branch from 'main', named `hotfix/<descriptive-name>`, e.g. `hotfix/component-x-failure`.
