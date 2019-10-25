<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AntoineTestControllerTest extends WebTestCase
{
    /*public function testCreerSortie()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/listeSorties');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $sortie=json_decode($client->getResponse()->getContent(),true);
        //var_dump($sortie);
        //$this->assertEquals('Erreur lors de la validation !',$sortie['messageErreur']);
        //$this->assertContains('Hello World', $crawler->filter('h1')->text());
    }*/

    public function testCreerSortie()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/listeSorties');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $sortie=json_decode($client->getResponse()->getContent(),true);
        //var_dump($sortie);
        //$this->assertEquals('Erreur lors de la validation !',$sortie['messageErreur']);
        //$this->assertContains('Hello World', $crawler->filter('h1')->text());
    }
}
