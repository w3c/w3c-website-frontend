# Testing

This application relies on PHPUnit along with Panther for application tests.

## Panther

[Panther](https://github.com/symfony/panther) is a library to scrape websites and to run end-to-end tests using real
browsers (and so executes Javascript and CSS).
It is based on the same API as [Symfony's WebTestCase](https://symfony.com/doc/current/testing.html#application-tests)
and as such is fully compatible with it, with some additional functions described in its documentation.

Using it requires [installing either chromedriver or geckodriver](https://github.com/symfony/panther#installing-chromedriver-and-geckodriver)
on the testing platform. For this we don't use the Composer alternative as it tends to have issues with the versions it
installs and recommend using the OS' package manager.

Basic usage requires for the test class to extend `PantherTestCase` and create a Panther client.

For more flexibility we can use the `PANTHER_BROWSER` environment variable to choose which driver to use (`chrome` or
`firefox`). This variable is not a part of Panther itself but allows testing the application against different browsers
on-the-fly.

Setting the `PANTHER_ERROR_SCREENSHOT_DIR` environment variable allows Panther to take screenshots when a test fails.

Setting the `PANTHER_NO_HEADLESS` environment variable will display the browser window, which can be useful to debug.

Here is some sample code of a test class using Panther: 
```php
use Symfony\Component\Panther\PantherTestCase;

class DefaultControllerTest extends PantherTestCase
{
    protected $client;

    public function setUp(): void
    {
        $browser = array_key_exists('PANTHER_BROWSER', $_SERVER) ? $_SERVER['PANTHER_BROWSER'] : self::CHROME;
        $this->client  = static::createPantherClient(['browser' => $browser]);
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', '/');
        $this->assertSelectorExists('body');
    }
}
```
