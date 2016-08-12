<?php

// src/OC/PlatformBundle/Controller/AdvertController.php


namespace OC\PlatformBundle\Controller;


use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Entity\AdvertSkill;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;


class AdvertController extends Controller
{
    public function indexAction($page)
    {
		//$content = $this->get('templating')->render('OCPlatformBundle:Advert:index.html.twig',array('listAdverts' => array()));

		$url = $this->get('router')->generate(
			'oc_platform_view', // name of route
			 array('id' => 5), //  les valeurs des paramètres
			UrlGeneratorInterface::ABSOLUTE_URL
		); // $url vaut "/platform/advert/5"
		
		// Accéder au container
		// $mailer = $this->container->get('mailer');
		
		// Get service Doctrine
		$doctrine = $this->get('doctrine');
		// Get EM
		$em = $doctrine->getManager();
		// Get repositories 
		$repository = $em->getRepository('OCPlatformBundle:Advert');
		
		$listAdverts = $repository->findAll();
		if($listAdverts === null) {
				throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
		}
		
		return $this->render('OCPlatformBundle:Advert:index.html.twig', array('listAdverts' => $listAdverts));
    }


    public function viewAction(Request $request) {

	$id = $request->attributes->get('id');
	// On récupère notre paramètre tag
	$tag = $request->query->get('tag');
	// We must have the same name than  of route ($id)
	// return new Response("Affichage de l'annonce d'id : ".$id.", avec le tag :". $tag);
	// return $this->render('OCPlatformBundle:Advert:view.html.twig', array('id' => $id, 'tag' => $tag, ))
    // return $this->get('templating')->renderResponse('OCPlatformBundle:Advert:view.html.twig', array('id' => $id, 'tag' => $tag));

	// Return Reposne HTTP de type redirection 
	$url = $this->get('router')->generate('oc_platform_home');
	// return new RedirectResponse($url);
	// return $this->redirect($url);
	// return $this->redirectToRoute('oc_platform_home');

	// Retourner JSON par exemple
	// $response = new Response(json_encode(array('id' => $id)));
	// $response->headers->set('Content-Type', 'application/json');
	// return $response;
	// return new JsonResponse(array('id' => $id));

	// Gerer les sessions
	// Récupération de la session
	//$session = $request->getSession();
	//$userId = $session->get('user_id');
	//$session->set('user_id', 91);

		// Get service Doctrine // $this->getDoctrine();
		$doctrine = $this->get('doctrine');
		// Get EM
		$em = $doctrine->getManager();
		// Get repositories 
		$repository = $em->getRepository('OCPlatformBundle:Advert');	  
		// On récupère l'entité correspondante à l'id $id
		$advert = $repository->find($id);
		if($advert === null) {
			throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
		}
		
		$listApplications = $em->getRepository('OCPlatformBundle:Application')->findBy(array('advert' => $advert));
		//$test = $advert->getApplications();
		//print_r($test); exit;
		$listAdvertSkills = $em->getRepository('OCPlatformBundle:AdvertSkill')->findBy(array('advert' => $advert));
		return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
			'advert' => $advert, 
			'listApplications' => $listApplications,
			'listAdvertSkills' => $listAdvertSkills,
			//'test' => $test,
		));
    }


    public function addAction(Request $request) {
	
		// On récupère l'EntityManager
		$em = $this->get('doctrine')->getManager();
		//$session = $request->getSession();
		//$session->getFlashBag()->add('info', 'Annonce bien enregistrée');;
		//$session->getFlashBag()->add('info', 'Oui Oui, bien enregistrée !!');
		//return $this->redirectToRoute('oc_platform_view', array('id' => 5));
		// Manipuler un service
		/*$antispam = $this->container->get('oc_platform.antispam');
		$text = "...";
		if($antispam->isSpam($text)) {
			throw new \Exception('Votre message a été détecté comme spam !');
		}*/
		$advert = new Advert;
		$advert->setTitle('Recherche CPT');
		$advert->setAuthor('Admin');
		$advert->setContent('Chef de projet technique à Labsara');
		
		// Création d'une 1er candidature 
		$application1 = new Application();
		$application1->setAuthor('Soukaina');
		$application1->setContent('J\'ai toutes les qualités requises');
		
		// Création d'une 2eme candidature 
		$application2 = new Application();
		$application2->setAuthor('Chelhi');
		$application2->setContent('Je ne suis plus motivée ');
		
		// liers les candidatures à l'annonce
		$application1->setAdvert($advert);
		$application2->setAdvert($advert);
		
		// Création de l'entité Image
		$image = new Image;
		$image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');
		$image->setAlt('Job de rêve');
		
		// On lié  l'image à l'annonce
		$advert->setImage($image);
		
		// Get All Skills
		$listSkills = $em->getRepository('OCPlatformBundle:Skill')->findAll();
		//  for evry skill
		foreach($listSkills as $skill) {
			// On créer une nouvelle relation entre 1 annonce et 1 compétence
			$advertskill = new AdvertSkill();
			$advertskill->setAdvert($advert);
			$advertskill->setSkill($skill);
			$advertskill->setLevel("Expert");
			// on persiste cette entité de relation propriétaire des deux  autres relations
			$em->persist($advertskill);
		}
		
		// Etape 1 : persister l'objet
		$em->persist($advert);
		// Si on  n'avait pas défini le cascade={"persist"},
		// on devrait persister à la main l'entité $image
		// $em->persist($image);
		
		// Etape 2 : On flush tout ce qui a été persisté avant
		
		$em->persist($application1);$em->persist($application2);
		
		$em->flush();
		
		if($request->isMethod('POST')) {
			$request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée');
			return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
		}
		
		return $this->render('OCPlatformBundle:Advert:add.html.twig');
    }

	
    public function editAction($id, Request $request) {
		$em = $this->get('Doctrine')->getManager();
		$advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);
		if($advert === null){
			throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas. ");
		}
		$listCategories = $em->getRepository('OCPlatformBundle:Category')->findAll();
		foreach($listCategories as $category) {
			$advert->addCategory($category);
		}
		// Pour persister le changement dans la relation, il faut persister l'entité propriétaire
		// Ici Advert est le propriétaire, donc inutile de la persister cat on l'a récupérée depuis Doctrine
		$em->flush();
		return $this->render('OCPlatformBundle:Advert:edit.html.twig', array('advert' => $advert ));
    }
	

    public function menuAction() {
		// Get service Doctrine
		$doctrine = $this->get('doctrine');
		// Get EM
		$em = $doctrine->getManager();
		// Get repositories 
		$repository = $em->getRepository('OCPlatformBundle:Advert');
		$listAdverts = $repository->findAll();
		return $this->render('OCPlatformBundle:Advert:menu.html.twig', array('listAdverts' => $listAdverts));
    }

	
    // Notez que l'ordre des arguments n'a pas d'importance
    // La route fait la correspondance à partir du nom des variables utilisées
    public function viewSlugAction($slug, $year, $_format){
		return new Response("On pourrait afficher l'annonce correspondant au slug '".$slug."', créée en ".$year." et au format ".$_format.".");
    }
	
	
	public function deleteAction($id) {
		$em = $this->get('doctrine')->getManager();
		$advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);
		if($advert === null){
			throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas. ");
		}
		foreach($advert->getCategories() as $category) {
			$advert->removeCategory($category);
		}
		// delete the applications with  avdert
		$listApplications = $em->getRepository('OCPlatformBundle:Application')->findBy(array('advert' => $advert));
		foreach($listApplications as $application) {
			$em->remove($application);
		}
		
		// delete the applications with  avdert
		$listAdvertSkill= $em->getRepository('OCPlatformBundle:AdvertSkill')->findBy(array('advert' => $advert));
		foreach($listAdvertSkill as $advertSkill) {
			$em->remove($advertSkill);
		}
		// delete the Advert
		$em->remove($advert);
		// Pour persister le changement dans la relation, il faut persister l'entité propriétaire
		// Ici Advert est le propriétaire, donc inutile de la persister cat on l'a récupérée depuis Doctrine
		$em->flush();
		$url = $this->get('router')->generate('oc_platform_home');
		return $this->redirect($url);
	}
	
	
	public function listadvertbycategoryAction($name){
		$em = $this->get('doctrine')->getManager();
		$listAdverts = $em->getRepository('OCPlatformBundle:Advert')->getAdvertWithCategories($name);
		if($listAdverts === null) {
				throw new NotFoundHttpException("Les annones n'existe pas.");
		}
		
		return $this->render('OCPlatformBundle:Advert:list.html.twig', array('listAdverts' => $listAdverts, 'name' => $name));
		
	}
}
