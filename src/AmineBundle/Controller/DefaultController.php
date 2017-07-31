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
	 * Get the 3 latest adverts
	 * @param
	 * @return 
     */	 
    public function indexAction()
    {
	// Get service Doctrine
	$this->doctrine = $this->get('doctrine');
	// Get EM
	$this->em = $this->doctrine->getManager();
	// Get repositories 
	$repository = $this->em->getRepository('OCPlatformBundle:Category');
	$listCategories = $repository->findAll();
        return $this->render('AmineBundle:Default:index.html.twig', array('listCategories' => $listCategories));
    }
	
	
	/**
	 * Display message (page of contact )
	 * 
	 */
	public function contactAction(Request $request) {
		/*$session = $request->getSession();
        $session->getFlashBag()->add('contact', 'La page de contact n’est pas encore disponible. Merci de revenir plus tard');
		return $this->redirectToRoute('amine_homepage');*/
		$contact = new Contact;
		$form = $this->get('form.factory')->create(ContactType::class, $contact);
		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			$sendMail = $this->get('oc_platform.email.application_mailer')->sendMailWebMaster($contact);
			$session = $request->getSession();			
			$session->getFlashBag()->add('contact', 'votre demande a été pris en compte');	
			return $this->redirect($this->generateUrl('amine_contact'));
		}

		return $this->render('AmineBundle:Default:contact.html.twig', array('form' => $form->createView()));

	}	
	
	public function headerAction($limit)
	{
		// Get service Doctrine
		$this->doctrine = $this->get('doctrine');
		// Get EM
		$this->em = $this->doctrine->getManager();
		// Get repositories 
		$repository = $this->em->getRepository('AmineBundle:Page');
		$listPages = $repository->findBy(array('isMenu' => true),array(), $limit, 0);
		return $this->render('AmineBundle:Default:header.html.twig', array('listPages' => $listPages));
    }
	
	
	public function pageAction($slug, Request $request)
	{
		if ($slug == "nous-contacter") {
			//$url = $this->get('router')->generate('oc_platform_home');
			return $this->redirectToRoute('amine_contact');
		}
		$this->doctrine = $this->get('doctrine');
		$this->em = $this->doctrine->getManager();
		$repository = $this->em->getRepository('AmineBundle:Page');
		$page = $repository->findOneBy(array('slug' => $slug),array());
        return $this->render('AmineBundle:Default:page.html.twig', array('page' => $page));
	}
}
