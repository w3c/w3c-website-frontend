# Continuous integration

We use [GitHub actions](https://docs.github.com/en/actions) to run automated actions on any merge into the default branch (main).

When your work includes writing some PHP, make sure to run these tests manually; at least before merging you working branch into 'main'.

## PHP
Config: .github/workflows/php.yml

Tests the project across different versions of PHP. The workflow runs:

* [PHP linting](https://github.com/studio24/project-base-template/blob/main/docs/continuous-integration.md#php-linting) - is the code syntax valid?
* [Code formatting](https://github.com/studio24/project-base-template/blob/main/docs/continuous-integration.md#code-formatting) - does the PHP code meet our coding standards?

### PHPUnit

You can run PHPUnit by using the command: `./bin/phpunit tests/`

### PHP linting
PHPLint tests PHP files for syntax errors.

Config: .phplint.yml

We exclude the vendor folder and test all other PHP files in the project.

You can run Phplint manually by using the command `vendor/bin/phplint`

### Code formatting
PHP_CodeSniffer (PHPCS) tests PHP files to ensure they meet coding standards.

Config: .phpcs.xml.dist

Coding standard enforced:

* PSR-12

You can run PHPCS manually by using the command `vendor/bin/phpcs`

### Fixing code issues automatically
Use PHP Code Beautifier and Fixer (phpcbf) to automatically fix code issues.

Run it manually by using the command `vendor/bin/phpcbf`