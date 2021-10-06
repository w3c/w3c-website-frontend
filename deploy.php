<?php
namespace Deployer;

require 'recipe/common.php';
require 'vendor/studio24/deployer-recipes/all.php';

/**
 * Deployment configuration variables - set on a per-project basis
 */

// Friendly project name
$project_name = 'W3C Frontend';

// The repo for the project
$repository = 'git@github.com:w3c/w3c-website-frontend.git';

// Array of remote => local file locations to sync to your local development computer
$sync = [

];

// Shared files that are not in git and need to persist between deployments (e.g. local config)
$shared_files = [
    '.env.local'
];

// Shared directories that are not in git and need to persist between deployments (e.g. uploaded images)
$shared_directories = [
    'var/log',
    'var/sessions'
];

// Sets directories as writable (e.g. uploaded images)
$writable_directories = [

];

/**
 * Apply configuration to Deployer
 *
 * Don't edit beneath here unless you know what you're doing!
 *
 * DO NOT store the Slack hook in a public repo
 */


set('application', $project_name);
set('repository', $repository);
set('shared_files', $shared_files);
set('shared_dirs', $shared_directories);
set('writable_dirs', $writable_directories);
set('sync', $sync);
set('http_user', 'www-data');
set('webroot', 'public');
set('keep_releases', 5);
set('git_tty', true);
set('allow_anonymous_stats', false);

// Default stage - prevents accidental deploying to production with dep deploy
set('default_stage', 'staging');

/*
 * Host information
 * These settings should not need amending
 * Additional hosts and stages can be added
 * by copying the entire host block
 * Host, stage and deploy path must be unique
 */

host('staging')
    ->stage('staging')
    ->user('studio24')
    ->hostname('128.30.54.149')
    ->set('deploy_path', '/var/www/frontend-staging')
    ->set('url', 'https://www-staging.w3.org')
    ->set('composer_options', '{{composer_action}} --no-dev --verbose --no-progress --no-interaction --optimize-autoloader');

host('development')
    ->stage('development')
    ->user('studio24')
    ->hostname('128.30.54.149')
    ->set('deploy_path', '/var/www/frontend-dev')
    ->set('url', 'https://www-dev.w3.org')
    ->set('composer_options', '{{composer_action}} --verbose --no-progress --no-interaction --optimize-autoloader');

host('s24-development')
    ->stage('s24-development')
    ->user('deploy')
    ->hostname('52.31.200.8')
    ->set('http_user', 'apache')
    ->set('deploy_path', '/data/var/www/vhosts/w3c/www-w3c/development')
    ->set('url', 'https://www-dev-w3c.studio24.dev')
    ->set('composer_options', '{{composer_action}} --verbose --no-progress --no-interaction --optimize-autoloader');


/**
 * Deployment task
 * The task that will be run when using dep deploy
 */
desc('Deploy ' . get('application'));
task('deploy', [

    // Run initial checks
    'deploy:info',

    // Remind user to check that the remote .env is up to date (development and staging (default N)
    'env-reminder',

    's24:check-branch',
    's24:show-summary',
    's24:display-disk-space',

    // Request confirmation to continue (default N)
    's24:confirm-continue',

    // Deploy site
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',

    // Deploy shared, writeable and clear paths
    'deploy:shared',
    'deploy:writable',
    'deploy:clear_paths',

    // Composer install
    'dump-env',
    'deploy:vendors',

    // Create build summary
    's24:build-summary',

    // Build complete, deploy is live once deploy:symlink runs
    'deploy:symlink',

    // Cleanup
    'deploy:unlock',
    'cleanup',
    'success'
]);

/**
 * Custom Tasks
 */

// Reminder to ensure remote .env file is upto date

desc('Remind user to update remote .env before continuing');
task('env-reminder', function () {

    $stage = get('hostname');

    writeln(' ');
    writeln("<fg=green;options=bold>Please double check whether you need to edit the .env.local file on the server for $stage</>");
    writeln(' ');
    if (!askConfirmation('Continue with deployment?')) {
        die('Ok, deployment cancelled.');
    }
});

desc('Dump env details for deployment');
task('dump-env', function () {
    writeln('composer dump-env');
});

desc('Clear cache after Composer install');
task('cache-clear', function () {
    writeln('php bin/console cache:clear');
});

// [Optional] If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
