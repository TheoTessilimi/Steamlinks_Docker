<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }
    public function testLoginPage(): void
    {
        $this->client->request('GET', '/login');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h1', 'Merci de vous connecter :');
    }

    public function testLoginPageFromHome(): void
    {
        $crawler = $this->client->request('GET', '/');
        $link = $crawler->selectLink('Connexion')->link();
        $this->client->click($link);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h1', 'Merci de vous connecter :');
    }
}
