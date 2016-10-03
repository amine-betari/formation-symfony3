<?php

namespace OC\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\UserBundle\Entity\User;

class LoadUser implements FixtureInterface
{
  public function load(ObjectManager $manager)
  { 
  
	// A revoir le sousci des mot de passes
	$listNames = array('amine-betari');
	foreach ($listNames as $name) {
	  $user = new User;
      $user->setUsername($name);
	  $user->setEnabled(true);
  	  $user->setPassword($name);
  	  //$user->setSalt('');
	  $user->setEmail('admin@hotmail.com');
 	  $user->setRoles(array('ROLE_ADMIN'));
	  $manager->persist($user);
	}
	$manager->flush();
  }
}
