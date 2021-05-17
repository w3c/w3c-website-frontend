<?php

namespace App\Tests\Controller;

use Symfony\Component\Panther\PantherTestCase;

class DefaultControllerTest extends PantherTestCase
{
    /**
     * @dataProvider provider
     */
    public function testIndex(string $lang, string $langPrefix, string $path): void
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', $langPrefix . $path);

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
