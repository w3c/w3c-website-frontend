<?php
namespace Deployer;

require 'vendor/studio24/deployer-recipes/recipe/default.php';
require 'contrib/php-fpm.php';

/**
 * Deployment configuration variables - set on a per-project basis
 */

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

set('http_user', 'www-data');

set('webroot', 'public');
set('git_tty', true);
set('allow_anonymous_stats', false);

set('git_ssh_command', 'ssh');
set( 'writable_mode', 'acl');
/*
 * Host information
 * These settings should not need amending
 * Additional hosts and stages can be added
 * by copying the entire host block
 * Host, stage and deploy path must be unique
 */

host('production')
    ->set('labels', ['stage' => 'production'])
    ->set('remote_user', 'studio24')
    ->set('hostname', 'leda.w3.internal')
    ->set('deploy_path', '/var/www/frontend')
    ->set('url', 'https://www.w3.org');

// Currently not in use
// host('staging')
//     ->stage('staging')
//     ->user('studio24')
//     ->hostname('128.30.54.149')
//     ->set('deploy_path', '/var/www/frontend-staging')
//     ->set('url', 'https://www-staging.w3.org')
//     ->set('composer_options', '{{composer_action}} --no-dev --verbose --no-progress --no-interaction --optimize-autoloader');

host('development')
    ->set('labels', ['stage' => 'development'])
    ->set('remote_user', 'studio24')
    ->set('hostname', 'thebe.w3.internal')
    ->set('deploy_path', '/var/www/frontend-dev')
    ->set('url', 'https://www-dev.w3.org')
    ->set('branch', 'update/deployer-7')
    ->set('composer_options', '--optimize-autoloader');

/**
 * Deployment task
 * The task that will be run when using dep deploy
 */
// desc('Deploy ' . get('application'));
// task('deploy', [

//     // Run initial checks
//     'deploy:prepare',

//     // Remind user to check that the remote .env is up to date (development and staging (default N)
//     'env-reminder',
    
//     'deploy:vendors',

//     // Dump environment file
//     // 'dump-env',

//     // Run deployment 
//     'deploy:clear_paths',
//     'deploy:publish'
// ]);

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

// PHP-FPM reload
after('deploy', 'php-fpm:reload');