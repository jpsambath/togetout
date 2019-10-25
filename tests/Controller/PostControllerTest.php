<?php

namespace App\Tests\Controller;

use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{



    public function testInscrireManuellementUtilisateur()
    {
        $user = static::createClient();


        $this->assertEquals(200, $user->getResponse()->getStatusCode());
        $user1 = json_decode($user->getResponse()->getContent(), true);


        $this->assertNotNull("nom");
        /*$this->assertNotNull("username");
        $this->assertNotNull("email");
        $this->assertNotNull("password");*/

    }

    /*public function testClotureInscrition()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/clotureInscription');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $inscription = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(date(), $inscription["dateLimiteInscription"]);

    }*/

    /*public function testAnnulationSortie()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/clotureInscription');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $inscription = json_decode($client->getResponse()->getContent(), true);



    }*/

    /*public function testConsulterProfil()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/consulterProfil/17');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $user1 = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(true, $user1["actif"]);

    }*/


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
