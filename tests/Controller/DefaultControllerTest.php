<?php

namespace App\Tests\Controller;

use Symfony\Component\Panther\PantherTestCase;

class DefaultControllerTest extends PantherTestCase
{
    protected $client;

    public function setUp(): void
    {
        $browser = array_key_exists('PANTHER_BROWSER', $_SERVER) ? $_SERVER['PANTHER_BROWSER'] : self::CHROME;
        $this->client  = static::createPantherClient(['browser' => $browser]);
    }

    /**
     * @dataProvider provider
     */
    public function testIndex(string $lang, string $langPrefix, string $path): void
    {
        $crawler = $this->client->request('GET', $langPrefix . $path);

        $this->assertSelectorAttributeContains('html', 'lang', $lang);
        $this->assertSelectorTextSame('#lang', $lang);
        $this->assertSelectorTextContains('#route', $path);
    }

    public function provider()
    {
        return [
            ['lang' => 'en', 'lang_prefix' => '', 'path' => '/'],
            ['lang' => 'en', 'lang_prefix' => '', 'path' => '/foo'],
            ['lang' => 'en', 'lang_prefix' => '', 'path' => '/foo/bar'],
            ['lang' => 'ja', 'lang_prefix' => '/ja', 'path' => '/'],
            ['lang' => 'ja', 'lang_prefix' => '/ja', 'path' => '/foo'],
            ['lang' => 'ja', 'lang_prefix' => '/ja', 'path' => '/foo/bar']
        ];
    }
}
