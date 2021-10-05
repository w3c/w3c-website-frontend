<?php
/**
 * Test a CSV list of URLs to ensure they are not 301 permanent redirects
 *
 * Useful to help validate new URLs are not already setup as 301 redirects
 *
 * Run this manually (this does not run as part of unit tests since tests live URLs)
 * php tests/urls/test-urls.php
 */

namespace Test;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\ResponseInterface;

require __DIR__ . '/../../vendor/autoload.php';

// Match status code
$statusCode = 301;

// Load URL CSV (read 1st column)
$urls = [];
if (($handle = fopen(__DIR__ . "/urls.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $urls[] = $data[0];
    }
    fclose($handle);
}

$client = HttpClient::create([
    'max_redirects' => 0,
]);
$responses = [];
$found = [];

echo sprintf("Testing %s URLs to see whether they are already setup as 301 redirects", count($urls));

foreach ($urls as $url) {
    $responses[] = $client->request('GET', $url);
}

/** @var ResponseInterface $response */
foreach ($responses as $response) {
    echo $response->getInfo('url') . ' ' . $response->getStatusCode() . PHP_EOL;

    if ($response->getStatusCode() === $statusCode) {
        $found[] = $response->getInfo('url');
    }
}

echo PHP_EOL;
echo sprintf("Found %s URLs that match %s:\n%s", count($found), $statusCode, implode(PHP_EOL, $found));

