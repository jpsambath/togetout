<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AntoineTestControllerTest extends WebTestCase
{
    public function testCreerSortie()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/listeSorties');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        //$sortie=json_decode($client->getResponse()->getContent(),true);
        //var_dump($sortie);
        //$this->assertEquals('Erreur lors de la validation !',$sortie['messageErreur']);
    }

    /*public function testCreerSortie()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/creerSortie');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        //$sortie=json_decode($client->getResponse()->getContent(),true);
    }*/

 /*   public function testSendMailRecuperationMDP()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/sendMailRecuperationMDP');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        //$sortie=json_decode($client->getResponse()->getContent(),true);
        //var_dump($sortie);
        //$this->assertEquals('Erreur lors de la validation !',$sortie['messageErreur']);
    }*/
}
