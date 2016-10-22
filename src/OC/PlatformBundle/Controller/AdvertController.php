<?php
// src/OC/PlatformBundle/Controller/AdvertController.php
namespace OC\PlatformBundle\Controller;

use OC\PlatformBundle\Event\PlatformEvents;
use OC\PlatformBundle\Event\MessagePostEvent;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Form\AdvertType;
use OC\PlatformBundle\Form\AdvertEditType;
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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
//use OC\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;




class AdvertController extends Controller
{
	/**
	 * @ParamConverter("json")
	 */
	public function ParamConverterAction($json)
	{
		return new Response(print_r($json, true));
	}
	
	
	public function translationAction($name) 
	{
		return $this->render('OCPlatformBundle:Advert:translation.html.twig', array(
			'name' => $name
		));
	}
	public function purgeAction($days)
	{
		$purge = $this->container->get('oc_platform.purger.advert');
		$purge->purge($days);
		return new Response('Adverts purgées');
	}
	
	
	public function testAction($id)
	{
		$advert = new Advert;
		$advert->setTitle("Recherche développeur !");
		$advert->setAuthor("A");
		$advert->setContent("Recherche développeur !");
		
		// On récupère le service validator
		$validator = $this->get('validator');
		
		// On déclenche la validation sur notre objet
		$listErrors = $validator->validate($advert);
		
		if(count($listErrors) > 0) {
			return new Response((string) $listErrors);
		} else {
			$em = $this->getDoctrine()->getManager();
			$em->persist($advert);
			$em->flush(); // c'est à ce moment qu'est généré le slug
			return new Response('Slug généré : '.$advert->getSlug());
		}
		
	}
	
    public function indexAction($page)
    {
		//if ($page < 1) throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		
		$url = $this->get('router')->generate(
			'oc_platform_view', // name of route
			 array('id' => 5), //  les valeurs des paramètres
			UrlGeneratorInterface::ABSOLUTE_URL
		); // $url vaut "/platform/advert/5"
		
		// Pagination : Fixer le nombre d'annonce par page à 3
		$nbPerPage = 3;
		// Get service Doctrine
		$doctrine = $this->get('doctrine');
		// Get EM
		$em = $doctrine->getManager();
		// Get repositories 
		$repository = $em->getRepository('OCPlatformBundle:Advert');
		$listAdverts = $repository->getAdverts($page, $nbPerPage);
		// Pagination : On calcule le nbr totale de pages
		$nbPages = ceil(count($listAdverts) / $nbPerPage);
		// Pagination :si la page n'existe pas, on retourne un 404
		if ($page > $nbPages) throw new NotFoundHttpException("La page ".$page." n'existe pas.");

		return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
			'listAdverts' => $listAdverts,
			'nbPages' => $nbPages,
			'page' => $page,
		));
    }


    public function viewAction(Request $request, Advert $advert) 
	{
		//echo $advert->getId(); exit;
		// Grâce à cette signature Advert $advert, nous venons d'économiser :
		// $em->find() ainsi que le if (null !== $advert)
		// On peut faire tout simplement $advert->getID();
	$id = $request->attributes->get('id');
	// return new Response("Affichage de l'annonce d'id : ".$id.", avec le tag :". $tag);
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
	// Get list application of advert
	$listApplications = $em->getRepository('OCPlatformBundle:Application')->findBy(array('advert' => $advert));
	//$test = $advert->getApplications();
	// Get list AdvertSkill of Advert
	$listAdvertSkills = $em->getRepository('OCPlatformBundle:AdvertSkill')->findBy(array('advert' => $advert));
	
	return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
			'advert' => $advert, 
			'listApplications' => $listApplications,
			'listAdvertSkills' => $listAdvertSkills,
		));
    }


    public function addAction(Request $request) 
	{
	
		if (!$this->get('security.authorization_checker')->isGranted('ROLE_AUTEUR')) {
			throw new AccessDeniedException('Accès limité aux auteurs.');
		}
		// On creér l'objet 
		$advert = new Advert;
		
		// On creér le formBuilder grâce au service form factory
		//$formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $advert);
		$form = $this->get('form.factory')->create(AdvertType::class, $advert);
		
		
		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			// On récupère l'EntityManager
			$em = $this->get('doctrine')->getManager();	
			
			// $user = $em->getRepository('OCUserBundle:User')->findBy(array('username' => 'soukaina'));
			
			
			$event = new MessagePostEvent($advert->getContent(), $this->getUser());

			// on déclenche l'événement
			$this->get('event_dispatcher')->dispatch(PlatformEvents::POST_MESSAGE, $event);
			
			// On fait le lien Request <=> Form
			// à partir du maintenant, la variable $advert contient les valeurs entrées dans le form par le visiteur
					//$form->handleRequest($request);
			// On vérifie que les valeurs entrées sont correctes
			//if($form->isValid()) {
			
				
			// Création de l'entité Image
			/*$image = new Image;
			$image->setUrl('https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcT3fWBugQz8xxR4ZhjMyox5CE3cjWsp9CgrOYp_H9EmEDb9hYCX');
			$image->setAlt('Job de rêve');
			// On lié  l'image à l'annonce
			$advert->setImage($image);*/
			
			// GE
				// On récupère ce qui a été modifié par le ou les listeners, ici le message
			// GE
			$advert->setContent($event->getMessage());
			
			$em->persist($advert);
			$em->flush();
			$request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée');
			return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
		}

		return $this->render('OCPlatformBundle:Advert:add.html.twig', array('form' => $form->createView()));

		// Manipuler un service
		/*$antispam = $this->container->get('oc_platform.antispam');
		$text = "...";
		if($antispam->isSpam($text)) {
			throw new \Exception('Votre message a été détecté comme spam !');
		}*/
		
		/*$advert = new Advert;
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
		
		// Création d'une 3eme candidature 
		$application3 = new Application();
		$application3->setAuthor('Amine');
		$application3->setContent('Lead Ded PHP');
		
		// liers les candidatures à l'annonce
		$application1->setAdvert($advert);
		$application2->setAdvert($advert);
		$application3->setAdvert($advert);
		
		// Création de l'entité Image
		$image = new Image;
		$image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');
		$image->setAlt('Job de rêve');
		
		// On lié  l'image à l'annonce
		$advert->setImage($image);
		
		// Get All Skills
		$listSkills = $em->getRepository('OCPlatformBundle:Skill')->findAll();
		//  for each skill
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
		
		$em->persist($application1);$em->persist($application2);$em->persist($application3);
		
		$em->flush();
		*/
    }

	
    public function editAction($id, Request $request) 
	{
		if (!$this->get('security.authorization_checker')->isGranted('ROLE_AUTEUR')) {
			throw new AccessDeniedException('Accès limité aux auteurs.');
		}
		
		$em = $this->get('Doctrine')->getManager();
		$advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);
		if($advert === null){
			throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas. ");
		}
		
		// On creér le formBuilder grâce au service form factory
		$form = $this->get('form.factory')->create(AdvertEditType::class, $advert);
	
		if($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			// On récupère l'EntityManager
			$em = $this->get('doctrine')->getManager();	
			//$em->persist($advert);
			$em->flush();
			$request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');
			return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
		}
		
		return $this->render('OCPlatformBundle:Advert:edit.html.twig', array('advert' => $advert , 'form' =>  $form->createView()));
		
		/*$listCategories = $em->getRepository('OCPlatformBundle:Category')->findAll();
		foreach($listCategories as $category) {
			$advert->addCategory($category);
		}*/
		// Pour persister le changement dans la relation, il faut persister l'entité propriétaire
		// Ici Advert est le propriétaire, donc inutile de la persister cat on l'a récupérée depuis Doctrine
    }
	

	public function deleteAction($id, Request $request) 
	{
		if (!$this->get('security.authorization_checker')->isGranted('ROLE_AUTEUR')) {
			throw new AccessDeniedException('Accès limité aux auteurs.');
		}
		
		$em = $this->get('doctrine')->getManager();
		$advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);
		if($advert === null){
			throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas. ");
		}
		
		// On créer un formulaire vide, qui ne contiendra que le champ CSRF
		// Cela permet de protéger la supression d'annonce contre cette faille
		$form = $this->get('form.factory')->create();
		if($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			$em->remove($advert);
			$em->flush();
			$request->getSession()->getFlashBag()->add('info', 'L\'annonce a bien été supprimé. ');
			return $this->redirectToRoute('oc_platform_home');
		}
		return $this->render('OCPlatformBundle:Advert:delete.html.twig', array(
			'advert' => $advert,
			'form'   => $form->createView(),
		));
		// on boucle sur les catégories de l'annonce pour les supprimer
		/*foreach($advert->getCategories() as $category) {
			$advert->removeCategory($category);
		}*/
		
		// delete the applications with  avdert
		/*$listApplications = $em->getRepository('OCPlatformBundle:Application')->findBy(array('advert' => $advert));
		foreach($listApplications as $application) {
			$em->remove($application);
		}
		*/
		
		// delete the adverrskills with  avdert
		/*$listAdvertSkill= $em->getRepository('OCPlatformBundle:AdvertSkill')->findBy(array('advert' => $advert));
		foreach($listAdvertSkill as $advertSkill) {
			$em->remove($advertSkill);
		}*/
	}
	

	public function menuAction($limit)
	{
		// Get service Doctrine
		$doctrine = $this->get('doctrine');
		// Get EM
		$em = $doctrine->getManager();
		// Get repositories 
		$repository = $em->getRepository('OCPlatformBundle:Advert');
		$listAdverts = $repository->findBy(array(), array('date' => 'desc'), $limit, 0);
		return $this->render('OCPlatformBundle:Advert:menu.html.twig', array('listAdverts' => $listAdverts));
    }

	
	public function listadvertbycategoryAction($name)
	{
		$em = $this->get('doctrine')->getManager();
		$listAdverts = $em->getRepository('OCPlatformBundle:Advert')->getAdvertWithCategories($name);
		if($listAdverts === null) {
				throw new NotFoundHttpException("Les annones n'existe pas.");
		}
		
		return $this->render('OCPlatformBundle:Advert:list.html.twig', array('listAdverts' => $listAdverts, 'name' => $name));
		
	}
	
	
	public function deleteapplicationAction($id)
	{
		$em = $this->get('doctrine')->getManager();
		$application = $em->getRepository('OCPlatformBundle:Application')->find($id);
		if($application === null){
			throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas. ");
		}
		// delete the Advert
		$em->remove($application);
		$em->flush();
		$url = $this->get('router')->generate('oc_platform_home');
		return $this->redirect($url);
	}
	
}
