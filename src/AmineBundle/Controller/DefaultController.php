<?php

namespace AmineBundle\Controller;

use OC\PlatformBundle\Entity\Advert;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;


class DefaultController extends Controller
{
	/**
	 * Get the 3 latest adverts
	 * @param
	 * @return 
     */	 
    public function indexAction()
    {
		// Get service Doctrine
		$doctrine = $this->get('doctrine');
		// Get EM
		$em = $doctrine->getManager();
		// Get repositories 
		$repository = $em->getRepository('OCPlatformBundle:Advert');
		$listAdverts = $repository->findAll();
        return $this->render('AmineBundle:Default:index.html.twig', array('listAdverts' => $listAdverts));
    }
	
	
	/**
	 * Display message (page of contact )
	 * 
	 */
	public function contactAction(Request $request) {
		$session = $request->getSession();
        $session->getFlashBag()->add('contact', 'La page de contact n’est pas encore disponible. Merci de revenir plus tard');
		return $this->redirectToRoute('amine_homepage');
	}	
}
