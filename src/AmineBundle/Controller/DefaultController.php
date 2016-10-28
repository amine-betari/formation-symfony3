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
}
