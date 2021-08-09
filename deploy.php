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
set('webroot', 'web');
set('keep_releases', 5);
set('git_tty', true);
set('allow_anonymous_stats', false);
set('composer_options', '{{composer_action}} --verbose --prefer-dist --no-progress --no-interaction --no-dev --optimize-autoloader');

// Default stage - prevents accidental deploying to production with dep deploy
set('default_stage', 'staging');

/*
 * Host information
 * These settings should not need amending
 * Additional hosts and stages can be added
 * by copying the entire host block
 * Host, stage and deploy path must be unique
 */


host('production')
    ->stage('production')
    ->user('studio24')
    ->hostname('128.30.54.149')
    ->set('deploy_path', '/var/www/frontend-prod')
    ->set('url', 'https://www.w3.org');

host('beta')
    ->stage('beta')
    ->user('studio24')
    ->hostname('128.30.54.149')
    ->set('deploy_path', '/var/www/frontend-beta')
    ->set('url', 'https://beta.w3.org');

host('staging')
    ->stage('staging')
    ->user('studio24')
    ->hostname('128.30.54.149')
    ->set('deploy_path', '/var/www/frontend-staging')
    ->set('url', 'https://www-staging.w3.org');

host('development')
    ->stage('development')
    ->user('studio24')
    ->hostname('128.30.54.149')
    ->set('deploy_path', '/var/www/frontend-dev')
    ->set('url', 'https://www-dev.w3.org');

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

    // Backup .env file on server
    'env-backup',

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

    // Composer install
    'deploy:vendors',

    'deploy:shared',
    'deploy:writable',

    'deploy:clear_paths',
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

// [Optional] If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
