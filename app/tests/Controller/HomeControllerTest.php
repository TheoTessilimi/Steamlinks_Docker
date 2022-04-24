<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    /** @noinspection CssInvalidPseudoSelector */
    public function testHomePage(){
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertSame(1, $crawler->filter('html:contains("Bienvenue sur mon site !")')->count());
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
}
