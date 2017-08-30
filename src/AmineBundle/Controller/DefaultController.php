<?php

namespace AmineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use OC\PlatformBundle\Entity\Advert;
use AmineBundle\Entity\Contact;
use AmineBundle\Form\ContactType;



class DefaultController extends Controller
{
     private $doctrine;
     private $em;


    /**
     * Get 
     * @param
     * @return 
     */	 
    public function indexAction(Request $request)
    {
		// Get service Doctrine
		$this->doctrine = $this->get('doctrine');
		// Get EM
		$this->em = $this->doctrine->getManager();
		// Get repositories 
		$repository = $this->em->getRepository('OCPlatformBundle:Category');
		$listCategories = $repository->findAll();
		$response = $this->render('AmineBundle:Default:index.html.twig', array('listCategories' => $listCategories));
		// model cache expiration
		//$response->setSharedMaxAge(180);
		//$response->setPublic();
		return $response;
    }
	
	
    /**
     * Display message (page of contact )
     * 
     */
    public function contactAction(Request $request) 
    {
		$contact = new Contact;
		$form = $this->get('form.factory')->create(ContactType::class, $contact);
		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			$sendMail = $this->get('oc_platform.email.application_mailer')->sendMailWebMaster($contact);
			$session = $request->getSession();			
			$session->getFlashBag()->add('contact', 'votre demande a été pris en compte');	
			return $this->redirect($this->generateUrl('amine_contact'));
		}
		
		$response = $this->render('AmineBundle:Default:contact.html.twig', array('form' => $form->createView()));
		//$response->setPublic();
		//$response->setSharedMaxAge(86400);
		return $response;
    }	

	
     public function headerAction($limit, Request $request)
     {
		// Get service Doctrine
		$this->doctrine = $this->get('doctrine');
		// Get EM
		$this->em = $this->doctrine->getManager();
		// Get repositories 
		$repository = $this->em->getRepository('AmineBundle:Page');
		$listPages = $repository->findBy(array('isMenu' => true),array(), $limit, 0);
		$response = $this->render('AmineBundle:Default:header.html.twig', array('listPages' => $listPages));
		// cache for 60 seconds
		// $response->setSharedMaxAge(60);
				// (optional) set a custom Cache-Control directive
				// $response->headers->addCacheControlDirective('must-revalidate', true);
		// $response->setEtag(md5(time()));
		//if( $response->isNotModified($request) ) 
		return  $response;
	
      }
	
	

	public function pageAction($slug, Request $request)
	{
		if ($slug == "nous-contacter") {
			return $this->redirectToRoute('amine_contact');
		}
		$this->doctrine = $this->get('doctrine');
		$this->em = $this->doctrine->getManager();
		$repository = $this->em->getRepository('AmineBundle:Page');
		$page = $repository->findOneBy(array('slug' => $slug),array());

		$response = $this->render('AmineBundle:Default:page.html.twig', array('page' => $page));
		// cache for 3600 seconds
	   	// $response->setSharedMaxAge(3600);

	      	// (optional) set a custom Cache-Control directive
		//    $response->headers->addCacheControlDirective('must-revalidate', true);
	        //  set one vary header
		// $response->setVary('Accept-Encoding');
	       	return  $response;
	}
}
