<?php

namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AmineBundle\Entity\Rubrique;

class LoadRubrique implements FixtureInterface
{
  // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
  public function load(ObjectManager $manager)
  {
    // Liste des noms de catégorie à ajouter
    $names = array(
//      array('')
      'Développement mobile',
      'Graphisme',
      'Intégration',
      'Réseau',
      'DevOps'
    );
    foreach ($names as $name) {
      // On crée la catégorie
      $category = new Category();
      $category->setName($name);
      // On la persiste
      $manager->persist($category);
    }
    // On déclenche l'enregistrement de toutes les catégories
    $manager->flush();
  }
}
