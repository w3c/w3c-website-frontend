<?php
namespace Deployer;

/**
 * 1. Deployer recipes we are using for this website
 */
require 'vendor/studio24/deployer-recipes/recipe/symfony.php';
require 'contrib/php-fpm.php';

/**
 * 2. Deployment configuration variables
 */

// Project name
set('application', 'W3C Frontend');

// Git repo
set('repository', 'git@github.com:w3c/w3c-website-frontend.git');

// Filesystem volume we're deploying to
set('disk_space_filesystem', '/var/www');

// W3C AWS config
set('http_user', 'www-data');
set('remote_user', 'studio24');
set('php_fpm_version', '8.2');

/**
 * 3. Hosts
 */
host('production')
    ->set('labels', ['stage' => 'production'])
    ->set('hostname', 'w')
    ->set('deploy_path', '/var/www/frontend')
    ->set('log_files', [
        'var/log/*.log',
        '/var/log/apache2/www.w3.org_access_ssl.log',
        '/var/log/apache2/www.w3.org_error_ssl.log',
    ])
    ->set('url', 'https://www.w3.org');

host('development')
    ->set('labels', ['stage' => 'development'])
    ->set('hostname', 'thebe.w3.internal')
    ->set('deploy_path', '/var/www/frontend-dev')
    ->set('log_files', [
        'var/log/*.log',
        '/var/log/apache2/www.w3.org_access_ssl.log',
        '/var/log/apache2/www.w3.org_error_ssl.log',
    ])
    ->set('url', 'https://www-dev.w3.org');


/**
 * 4. Deployment tasks
 *
 * Any custom deployment tasks to run
 */

// PHP-FPM reload
after('deploy', 'php-fpm:reload');