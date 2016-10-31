<?php

namespace AmineBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testContact()
    {
		// Cette méthode retourne un client, qui est comme un navigateur
		/*
		$client = static::createClient(array(
			'environment' = > 'my_test_env',
			'debug'       = > false,
		));
		*/
        $client = static::createClient();
		// La méthode request retourne un objet Crawler qui peut être utilisé pour sélectionner des éléments dans la réponse
		// Le crawler ne fonctionne que lorsque la réponse est un fichier XML ou un document HTML
		// Pour obtenir la réponse du contenu brut, appelez $client->getResponse()->getContent()
		// La signature de la méthode request est :
		/*
		request(
			$method,
			$uri,
			array $parameters = array(),
			array $files = array(),
			array $server = array(),
			$content = null,
			$changeHistory = true
		)
		*/
		// les méthodes 
        $crawler = $client->request('GET', '/nous-contacter');
		$this->assertEquals(1, $crawler->filter('h1:contains("Contact Amine")')->count());

		// Traitement formulaire	
		$form = $crawler->selectButton('Valider')->form();
		$form['contact[name]'] = 'name of name of contact';
		$form['contact[email]'] = 'abetari@sqli.com';
		$form['contact[subject]'] = 'Subject of Subject';
		$form['contact[body]'] = 'The comment body The comment body The comment body The comment body';
		// Cocher une case à cocher $form['varName']->tick();
		// Choisir une option ou un élément radio $form['varName']->select('Male');
		// End Traitement formulaire
		
		$crawler = $client->submit($form);
		// Debut Send mail, vérification de l'envoie de mail 
		if ($profile = $client->getProfile()) {
			$swiftMailerProfiler = $profile->getCollector('swiftmailer');
			// Seul 1 message doit avoir été envoyé 
			$this->assertEquals(1, $swiftMailerProfiler->getMessageCount());
			// On récupère le premier message 
			$messages = $swiftMailerProfiler->getMessages();
			$message = array_shift($messages);
			$contactEmail = $client->getContainer()->getParameter('blogger_blog.emails.contact_email');
			// On vérifier que le message a été envoyé à la  bonne adresse
			$this->assertArrayHasKey($contactEmail, $message->getTo());
		}
		// End send Mail
		// Forcer le client à suivre  la redirection
		$crawler = $client->followRedirect();
		$this->assertEquals(1, $crawler->filter('html:contains("votre demande a été pris en compte")')->count());
    }
}
