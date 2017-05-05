<?php
// src/OC/PlatformBundle/DataFixtures/ORM/LoadSkill.php

namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Advert;

class LoadAdvert implements FixtureInterface
{
  public function load(ObjectManager $manager)
  {
    // Liste des noms de compétences à ajouter
    $names = array(
		array('Consultant C++', 'Nous cherchons un développeur C++ à paris', 'Admin', new \Datetime('20 days ago')),
		array('Consultant Java', 'Nous cherchons un développeur Java  à casa', 'Admin', new \Datetime('6 days ago')),
		array('Consultant Drupal 7', 'Nous cherchons un bras cassé Drupal 7', 'Admin', new \Datetime()),
	);

    foreach ($names as $name) {
          // On crée la l'annonce
	  $advert = new Advert();
	  $advert->setTitle($name[0]);
	  $advert->setContent($name[1]);
	  $advert->setAuthor($name[2]);
	  $advert->setUpdatedAt($name[3]);
      	  // On la persiste
      	  $manager->persist($advert);
    }

    // On déclenche l'enregistrement de toutes les catégories
    $manager->flush();
  }
}
