<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JsonControllerTest extends WebTestCase
{
    /**
     * @dataProvider languageProvider
     */
    public function testTranslatedMessages(string $prefix): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $prefix . '/translated-messages');
        $messages = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('messages', $messages);
        $this->assertArrayHasKey('w3c_website_templates_bundle', $messages);
    }

    public function languageProvider(): array
    {
        return [
            ['prefix' => ''],
            ['prefix' => '/ja']
        ];
    }
}
