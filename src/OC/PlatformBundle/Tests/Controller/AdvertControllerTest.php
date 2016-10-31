<?php

namespace OC\PlatformBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdvertControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

		// l'objet crawler nous donne la possibilité de parcourir la réponse HTML / XML
		
		// 1er test
        // $crawler = $client->request('GET', '/test/2');
		// $this->assertContains('Slug généré : ', $client->getResponse()->getContent());
		// $this->assertEquals(1, $crawler->filter('h1:contains("Slug généré : ")')->count()); 
		
		// 2eme test
		$crawler = $client->request('GET', '/fr/platform');
		$this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Liste des annonces")')->count()
        );
		
		// 3eme test
		$advertLink = $crawler->filter('a.link-article')->first();
		$advertTitle = $advertLink->text();
		// print_r($advertLink->link());
		// print_r($advertLink->text());
		// print_r(trim($advertLink->text()));
		// exit;	
		$crawler = $client->click($advertLink->link());
		$this->assertEquals(1, $crawler->filter('h2:contains("'.trim($advertTitle).'")')->count()); 
		// $this->assertEquals(1, $crawler->filter('html:contains("Recherche développeur !")')->count()); 
	
    }
}
