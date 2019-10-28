<?php

namespace App\Tests\Controller;

use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{



    /*public function testInscrireManuellementUtilisateur()
    {
        $client = self::createClient();
        $client->request(
            'POST',
            '/inscrireUtilisateur/17',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"name":"yo"}'
        );

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals("utilisateur inscrit!", $response);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $clientdeserialiser = json_decode($client->getResponse()->getContent(), true);

    }*/

    /*public function testClotureInscrition()
    {
        $client = self::createClient();
        $crawler = $client->request(
            'POST',
            clotureInscription/1,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json']
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $inscription = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(date(), $inscription["dateLimiteInscription"]);

    }*/

    /*public function testAnnulationSortie()
    {
        $client = static::createClient();
        $crawler = $client->request(
            'POST',
            'annulerSortie/1',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"name":"yo"}'


        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $inscription = json_decode($client->getResponse()->getContent(), true);
    }*/

    public function testConsulterProfil()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/consulterProfil/17');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $user1 = json_decode($client->getResponse()->getContent(), true);

            var_dump($user1);
        $this->assertEquals(true, $user1["actif"]);

    }


    /*public function testAjoutLieu()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/ajoutLieu');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $lieu = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotNull("nom");
        $this->assertNotNull("ville");

    }*/

   /* public function testAjoutVille()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/ajoutVille');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $Ville = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotNull("nom");
        $this->assertNotNull("codePostal");

    }*/

}
