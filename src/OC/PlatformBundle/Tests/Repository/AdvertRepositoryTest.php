<?php
// src/Blogger/BlogBundle/Tests/Entity/BlogTest.php

namespace OC\PlatformBundle\Tests\Repository;


use OC\PlatformBundle\Entity\Repository\AdvertRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdvertRepositoryTest extends WebTestCase 
{
	/**
	 * @var \OC\PlatformBundle\Repository\AdvertRepository
	 */
	private $advertRepository;

	
	public function setUp()
	{
		$kernel = static::createKernel();
		$kernel->boot();
		$this->advertRepository = $kernel->getContainer()->get('doctrine.orm.entity_manager')->getRepository('OCPlatformBundle:Advert');
	}
	
	
	public function testGetAdvert()
	{
		$adverts = $this->advertRepository->myFindAllDQL();
		$this->assertTrue(count($adverts) > 1);
		// $this->assertContains('', $adverts);
		$advertByAuthor = $this->advertRepository->findByAuthorAndDate('AdminWeb', '2016');
		$this->assertEmpty($advertByAuthor);
	}
}