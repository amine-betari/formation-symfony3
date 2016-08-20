<?php

namespace OC\PlatformBundle\Purge;

use OC\PlatformBundle\Repository\AdvertRepository;


class AdvertArchive
{

	private $em;
	
	public function __construct(\Doctrine\ORM\EntityManager $em) 
	{
		$this->em =  $em;
	}
	
	
	/**
	 * pure all adverts without application and > days
	 * @param $days
	 */
	public function purge($days) 
	{
		// call methods from repository
		// get Service un service
		$em = $this->em;
		// Get EM
		// $em = $doctrine->getManager();
		// Get repositories 
		$repository = $em->getRepository('OCPlatformBundle:Advert');
		$listAdverts = $repository->getAdvertsWithoutCandidatureBeforeDays($days);
		// boucler et suprpimer 
		foreach($listAdverts as $advert) {
			$em->remove($advert);
		}
		$em->flush();

	}
}


