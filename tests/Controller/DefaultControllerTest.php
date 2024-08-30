<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Panther\PantherTestCase;

class DefaultControllerTest extends PantherTestCase
{
    protected $client;

    public function setUp(): void
    {
        $browser = array_key_exists('PANTHER_BROWSER', $_SERVER) ? $_SERVER['PANTHER_BROWSER'] : self::CHROME;
        $this->client  = static::createPantherClient(['browser' => $browser]);
    }

    #[DataProvider('provider')]
    public function testIndex(string $lang, string $title, string $langPrefix, string $path): void
    {
        $crawler = $this->client->request('GET', $langPrefix . $path);

        $this->assertSelectorAttributeContains('html', 'lang', $lang);
        $this->assertSelectorTextSame('h1', $title);
    }

    public static function provider()
    {
        return [
            ['lang' => 'en', 'title' => 'We believe in one web for all', 'langPrefix' => '', 'path' => '/'],
            ['lang' => 'en', 'title' => 'Ecosystems', 'langPrefix' => '', 'path' => '/ecosystems/'],
            ['lang' => 'en', 'title' => 'Blog listing', 'langPrefix' => '', 'path' => '/blog/'],
            //['lang' => 'ja', 'title' => 'W3C Home', 'langPrefix' => '/ja', 'path' => '/'],
            //['lang' => 'ja', 'title' => '日本語で Landing Page', 'langPrefix' => '/ja', 'path' => '/landing-page/'],
            ['lang' => 'ja', 'title' => 'Blog listing', 'langPrefix' => '/ja', 'path' => '/blog/']
        ];
    }
}
