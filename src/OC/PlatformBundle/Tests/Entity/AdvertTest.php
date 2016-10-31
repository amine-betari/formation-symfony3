<?php
// src/Blogger/BlogBundle/Tests/Entity/BlogTest.php

namespace OC\PlatformBundle\Tests\Entity;


use OC\PlatformBundle\Entity\Advert;

class BlogTest extends \PHPUnit_Framework_TestCase
{
	public function testGeneral()
	{
		$advert = new Advert;
		$this->assertEquals(0, $advert->getNbApplications());
		$this->assertTrue($advert->getPublished());
	}
	
	
	public function testSetSlug()
	{
		$advert = new Advert;
		$advert->setSlug('Amine BETARI');
		$this->assertEquals('amine-betari', $advert->getSlug());
	}
}