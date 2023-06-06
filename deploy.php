<?php
namespace Deployer;

require 'recipe/common.php';
require 'vendor/studio24/deployer-recipes/recipe/common.php';

/**
 * Deployment configuration variables - set on a per-project basis
 */

// Friendly project name
// $project_name = 'W3C Frontend';

// The repo for the project
// $repository = 'git@github.com:w3c/w3c-website-frontend.git';

// Array of remote => local file locations to sync to your local development computer
$sync = [

];

// Shared files that are not in git and need to persist between deployments (e.g. local config)
// $shared_files = [
//     '.env.local'
// ];

// Shared directories that are not in git and need to persist between deployments (e.g. uploaded images)
// $shared_directories = [
//     'var/log',
//     'var/sessions'
// ];

// Sets directories as writable (e.g. uploaded images)
$writable_directories = [
    'var/cache'
];

/**
 * Apply configuration to Deployer
 *
 * Don't edit beneath here unless you know what you're doing!
 *
 * DO NOT store the Slack hook in a public repo
 */


set('application', 'W3C Frontend');
set('repository', 'git@github.com:w3c/w3c-website-frontend.git');
set('shared_files', ['.env.local']);
set('shared_dirs', [
        'var/log',
        'var/sessions'
]);
set('writable_dirs', ['var/cache']);
set('sync', $sync);
set('http_user', 'www-data');
set('webroot', 'public');
set('git_tty', true);
set('allow_anonymous_stats', false);

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
    ->hostname('128.30.52.34')
    ->set('deploy_path', '/var/www/frontend')
    ->set('url', 'https://www.w3.org')
    ->set('composer_options', '{{composer_action}} --no-dev --verbose --no-progress --no-interaction --optimize-autoloader');

// Currently not in use
// host('staging')
//     ->stage('staging')
//     ->user('studio24')
//     ->hostname('128.30.54.149')
//     ->set('deploy_path', '/var/www/frontend-staging')
//     ->set('url', 'https://www-staging.w3.org')
//     ->set('composer_options', '{{composer_action}} --no-dev --verbose --no-progress --no-interaction --optimize-autoloader');

host('development')
    ->stage('development')
    ->user('studio24')
    ->hostname('128.30.54.149')
    ->set('deploy_path', '/var/www/frontend-dev')
    ->set('url', 'https://www-dev.w3.org')
    ->set('composer_options', '{{composer_action}} --verbose --no-progress --no-interaction --optimize-autoloader');

/**
 * Deployment task
 * The task that will be run when using dep deploy
 */
desc('Deploy ' . get('application'));
task('deploy', [

    // Run initial checks
    'deploy:prepare',

    // Remind user to check that the remote .env is up to date (development and staging (default N)
    'env-reminder',

    // Dump environment file
    'dump-env',

    // Run deployment 
    'deploy:vendors',
    'deploy:clear_paths',
    'deploy:publish'
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
after('deploy:failed', 'rollback');
