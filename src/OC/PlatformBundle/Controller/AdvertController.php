<?php
// src/OC/PlatformBundle/Controller/AdvertController.php
namespace OC\PlatformBundle\Controller;

use OC\PlatformBundle\Event\PlatformEvents;
use OC\PlatformBundle\Event\MessagePostEvent;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Form\AdvertType;
use OC\PlatformBundle\Form\AdvertEditType;
use OC\PlatformBundle\Form\ApplicationType;
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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Monolog\Logger;



class AdvertController extends Controller
{


	/**
     * set datatable configs
     * @return \Waldo\DatatableBundle\Util\Datatable
     */
    private function datatableAdmin() 
	{
		if (!$this->get('security.authorization_checker')->isGranted('ROLE_AUTEUR')) {
			throw new AccessDeniedException('Accès limité aux auteurs.');
		}

		$datatable =  $this->get('datatable')
					->setEntity('OCPlatformBundle:Advert', 'a')
					->setFields(
						array(
							"Name"		   			=> "a.title",
							"Nombre postulé"        => "a.nbApplications",
							"Compétences demandées" => "a.published",
							"Actions" 				=> "a.title",
							"_identifier_" => "a.id")
						)
					->setWhere(
						'a.author = :author',
						array('author' => $this->getUser()) 
						)						
					 ->setRenderers( array(
								0 => array(
									'view' => 'OCPlatformBundle:Advert:_actions.html.twig'
								),
								2 => array(
									'view' => 'OCPlatformBundle:Advert:_skills.html.twig'
								),
								3 => array(
									'view' => 'OCPlatformBundle:Advert:_actions_admin.html.twig'
								),
							)
						)
					->setOrder('a.date', 'desc')
					->setGlobalSearch(true);
		return $datatable;
	}


	/**
   	 * set datatable configs
	 * @return \Waldo\DatatableBundle\Util\Datatable
	 */
	private function datatable() 
	{
		$datatable =  $this->get('datatable')
			    ->setEntity('OCPlatformBundle:Advert', 'a')
			    ->setFields(
				array(
					"Name"   => "a.title",
					"Description" => "a.descriptif",
					"Compétences demandées" => "a.title",
					"_identifier_" => "a.id")
				)
			     ->setRenderers(
	                            array(
        	                        0 => array(
		               	              	'view' => 'OCPlatformBundle:Advert:_actions.html.twig'
        	                        ),
					2 => array(
						'view' => 'OCPlatformBundle:Advert:_skills.html.twig'
					),
                	            )
	                    )
			    ->setOrder('a.published', 'desc')
			    ->setGlobalSearch(true);
		return $datatable;
	}

	/**
	 * Grid action
	 * @return Response
	 */
	public function gridAction($slug) 
	{
		if ($slug == 'back')  return $this->datatableAdmin()->execute();
		if ($slug == 'front') return $this->datatable()->execute();
	}


	/**
	 * @ParamConverter("json")
	 */
	public function paramConverterAction($json)
	{
		return new Response(print_r($json, true));
	}


	/**
	 *
	 *
	 */
	public function dataAction() 
	{
		$this->datatable();
		return $this->render('OCPlatformBundle:Advert:data.html.twig');
	}

	
	/**
     *
 	 */
	public function dataAdminAction()
	{
		$this->datatableAdmin();
		return $this->render('OCPlatformBundle:Advert:data_admin.html.twig');
	}

	/**
	 * 
	 * 
	 */	
	public function deleteRelationAction(Request $request)
	{
		if($request->isXMLHttpRequest()) {
			$idSkill = $request->get('id');
			$idAdvert = $request->get('idAdvert');
			// Get Repository
			$doctrine = $this->get('doctrine');
			$em = $doctrine->getManager();
			$repository = $em->getRepository('OCPlatformBundle:AdvertSkill');
			// It Must used ParamConverter
			$advert = $em->getRepository('OCPlatformBundle:Advert')->find((int)$idAdvert);
			// It Must used ParamConverter
			$advertskill = $repository->findOneBy(array('advert' => (int)$idAdvert, 'skill' => (int)$idSkill ));
			// Delete this relation
			$advert->removeAdvertskille($advertskill);
			return new JsonResponse(array('data' => array($advertskill)));
		}
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
	
	
	
	public function indexAction($page)
	{
		// if recruteur redirect  to advertlist
		if ($this->get('security.authorization_checker')->isGranted('ROLE_RECRUTEUR')) {
			return $this->redirectToRoute('oc_platform_admin_datatable');
		}
		// Pagination : Fixer le nombre d'annonce par page à 3
		$nbPerPage = 3;
		// Get service Doctrine
		$doctrine = $this->get('doctrine');
		// Get EM
		$em = $doctrine->getManager();
		// Get repositories 
		$repository = $em->getRepository('OCPlatformBundle:Advert');
		$listAdverts = $repository->getAdverts($page, $nbPerPage);
//		if ($listAdverts === null) 
//		var_dump(count($listAdverts)); 
		if(count($listAdverts) == 0) $listAdverts = null;
		// Pagination : On calcule le nbr totale de pages
		$nbPages = ceil(count($listAdverts) / $nbPerPage);
		// Pagination :si la page n'existe pas, on retourne un 404
		// if ($page > $nbPages) throw new NotFoundHttpException("La page ".$page." n'existe pas.");
		return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
			'listAdverts' => $listAdverts,
			'nbPages' => $nbPages,
			'page' => $page,
		));
	}


	public function viewAction(Request $request, Advert $advert) 
	{	
		// Grâce à cette signature Advert $advert, nous venons d'économiser :
		// $em->find() ainsi que le if (null !== $advert)
		// On peut faire tout simplement $advert->getID();
		$id = $request->attributes->get('id');
		$url = $this->get('router')->generate('oc_platform_home');
		// return new RedirectResponse($url);
		// return $this->redirect($url);
		// return $this->redirectToRoute('oc_platform_home');

		// Retourner JSON par exemple
	//	 $doctrine = $this->get('doctrine');
	//         $em = $doctrine->getManager();
	//         $repository = $em->getRepository('OCPlatformBundle:Advert');      
	//	 $advert = $repository->find($id);
	//	 $response = new Response(json_encode(array('advert' => $advert)));
	//	 $response->headers->set('Content-Type', 'application/json');
	//	 return $response;
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
		// Get list AdvertSkill of Advert
		$listAdvertSkills = $em->getRepository('OCPlatformBundle:AdvertSkill')->findBy(array('advert' => $advert));

		// Create a new Entity of Application
		$application = new Application();
		// Get Form Application 
		$form = $this->get('form.factory')->create(ApplicationType::class, $application, array('user' => $this->getUser()));
		// Is anonumous
		if($this->getUser() && $this->getUser()->getMode() == 'candidat') $anonymous = 1;
		else $anonymous = 0;
		if($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			// Gestion de brochures est au niveau des listener
			$application->setAdvert($advert);
			$application->setAuthor($this->getUser());
			// $advert->setNbApplications($advert->getNbApplications()+1);
			$em->persist($application);
			$em->flush();
			// Display message flash for internaute
			$request->getSession()->getFlashBag()->add('noticeA', 'Votre candidature a été enregistré');
		}
	
	
		$response =  $this->render('OCPlatformBundle:Advert:view.html.twig', array(
				'form' =>  $form->createView(),
				'advert' => $advert, 
				'listApplications' => $listApplications,
				'listAdvertSkills' => $listAdvertSkills,
				'anonymous' => $anonymous
			));
		// Model cache validation
		
		//$response->setEtag(md5($advert->getDescriptif()));
		//$response->setSharedMaxAge(2);
		//$response->setLastModified($advert->getUpdatedAt());
		//$response->setPublic();
		//$response->isNotModified($request);
		return $response;
    }


    public function addAction(Request $request) 
    {
		if (!$this->get('security.authorization_checker')->isGranted('ROLE_AUTEUR')) {
			throw new AccessDeniedException('Accès limité aux auteurs.');
		}
		$advert = new Advert;
		// On creér le formBuilder grâce au service form factory
		//$formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $advert);
		$form = $this->get('form.factory')->create(AdvertType::class, $advert, array('user' => $this->getUser()));
		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			// exit(\Doctrine\Common\Util\Debug::dump($form->getData()));
			// On récupère l'EntityManager
			$em = $this->get('doctrine')->getManager();
			$event = new MessagePostEvent($advert->getDescriptif(), $this->getUser());
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
			$advert->setDescriptif($event->getMessage());
			// Get Data of AdvertSKill from Form
			$ads = $advert->getAdvertskilles();
			foreach($ads as $s)
			{
			 // Set Current Advert
			     $s->setAdvert($advert);
			 // Re-set the AdvertSkills in Advert object
	       		     $advert->addAdvertskille($s);
			}

			$em->persist($advert);
			$em->flush();
			$request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée');
			return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
		}
		return $this->render('OCPlatformBundle:Advert:add.html.twig', array('form' => $form->createView()));
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
		// Test if user can dit advert
		if( $advert->getAuthor() !== $this->getUser() ) {
			throw new NotFoundHttpException("Tu triches !!");
		}
		
		// On creér le formBuilder grâce au service form factory
		$form = $this->get('form.factory')->create(AdvertEditType::class, $advert, array('user' => $this->getUser()));
	
		if($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

			 $logger = $this->get('logger');
        		// $logger->info('Tout va bien, je suis en version 3.1.0');
	               	// Get Data of AdvertSKill from Form
			$ads = $advert->getAdvertskilles();
			$logger->critical('AdvertSkill '.count($ads));
 			$logger->critical('AdvertSkill Form '.count($form['advertskilles']->getData()));
			foreach($ads as $s)
			{
			 // Set Current Advert
				$s->setAdvert($advert);
			 // Re-set the AdvertSkills in Advert object
       			$advert->addAdvertskille($s);
			}
			// On récupère l'EntityManager
			$em = $this->get('doctrine')->getManager();	
			//$em->merge($advert);
			$em->flush();
			$request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');
			return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
		}
		
		return $this->render('OCPlatformBundle:Advert:edit.html.twig', array('advert' => $advert , 'form' =>  $form->createView()));
	
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
		if( $advert->getAuthor() !== $this->getUser() ) {
			throw new NotFoundHttpException("Tu triches !!");
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
	}
	

	public function menuAction($limit)
	{
		// Get service Doctrine
		$doctrine = $this->get('doctrine');
		// Get EM
		$em = $doctrine->getManager();
		// Get repositories 
		$repository = $em->getRepository('OCPlatformBundle:Advert');
		$listAdverts = $repository->findBy(array('published' => true), array('date' => 'desc'), $limit, 0);
		return $this->render('OCPlatformBundle:Advert:menu.html.twig', array('listAdverts' => $listAdverts));
    }
	
	public function menuRecruteurAction($limit)
	{
		// Get service Doctrine
		$doctrine = $this->get('doctrine');
		// Get EM
		$em = $doctrine->getManager();
		// Get repositories 
		$repository = $em->getRepository('OCPlatformBundle:Advert');
		$listAdvertsByRecruteur = $repository->findBy(
			array('published' => true, 'author'  => $this->getUser() ), 
			array('date' => 'desc'),
			//array('author' => $this->getUser()), 
			$limit, 0);
		return $this->render('OCPlatformBundle:Advert:menuRecruteur.html.twig', array('listAdverts' => $listAdvertsByRecruteur));
    }
	

	
	public function listAdvertByCategoryAction($id)
	{
		$em = $this->get('doctrine')->getManager();
		$listAdverts = $em->getRepository('OCPlatformBundle:Advert')->getAdvertWithCategories($id);
		if($listAdverts === null) {
				throw new NotFoundHttpException("Les annones n'existe pas.");
		}
		// CategoryName
		$category =  $em->getRepository('OCPlatformBundle:Category')->find($id);
		return $this->render('OCPlatformBundle:Advert:list.html.twig', array('listAdverts' => $listAdverts, 'name' => $category->getName()));
		
	}
	
	
	public function deleteApplicationAction($id)
	{
		$em = $this->get('doctrine')->getManager();
		$application = $em->getRepository('OCPlatformBundle:Application')->find($id);
		if($application === null){
			throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas. ");
		}
		// delete the application
		// $application->getAdvert()->setNbApplications($application->getAdvert()->getNbApplications()-1);
		$em->remove($application);
		$em->flush();
		$url = $this->get('router')->generate('oc_platform_view', array(
			'id' => $application->getAdvert()->getId()),
			 UrlGeneratorInterface::ABSOLUTE_URL
			); 
		return $this->redirect($url);
	}
	
}
