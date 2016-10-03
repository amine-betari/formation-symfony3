<?php

namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use OC\PlatformBundle\Entity\Skill;
use OC\PlatformBundle\Form\SkillType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


class SkillController extends Controller
{
    public function indexAction($page)
	{
		//if ($page < 1) throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		
		// Pagination : Fixer le nombre d'annonce par page à 3
		$nbPerPage = 12;
		// Get service Doctrine
		$doctrine = $this->get('doctrine');
		// Get EM
		$em = $doctrine->getManager();
		// Get repositories 
		$repository = $em->getRepository('OCPlatformBundle:Skill');
		$listSkills= $repository->getSkills($page, $nbPerPage);
		// Pagination : On calcule le nbr totale de pages
		$nbPages = ceil(count($listSkills) / $nbPerPage);
		// Pagination :si la page n'existe pas, on retourne un 404
		if ($page > $nbPages) throw new NotFoundHttpException("La page ".$page." n'existe pas.");

		return $this->render('OCPlatformBundle:Skill:index.html.twig', array(
			'listSkills' => $listSkills,
			'nbPages' => $nbPages,
			'page' => $page,
		));
	}
	
	
	public function addAction(Request $request) 
	{
		if (!$this->get('security.authorization_checker')->isGranted('ROLE_AUTEUR')) {
			throw new AccessDeniedException('Accès limité aux auteurs.');
		}
		// On creér l'objet 
		$skill = new Skill;
		
		// On creér le formBuilder grâce au service form factory
		//$formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $advert);
		$form = $this->get('form.factory')->create(SkillType::class, $skill);
		
		
		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			// On récupère l'EntityManager
			$em = $this->get('doctrine')->getManager();	
			$em->persist($skill);
			$em->flush();
			$request->getSession()->getFlashBag()->add('notice', 'Skill bien enregistrée');
			return $this->redirectToRoute('oc_platform_view_skill', array('id' => $skill->getId()));
		}

		return $this->render('OCPlatformBundle:Skill:add.html.twig', array('form' => $form->createView()));
    }
	
	
	public function editAction(Request $request, Skill $skill) 
	{
		if (!$this->get('security.authorization_checker')->isGranted('ROLE_AUTEUR')) {
			throw new AccessDeniedException('Accès limité aux auteurs.');
		}
		
		$em = $this->get('Doctrine')->getManager();
		// $skill = $em->getRepository('OCPlatformBundle:Skill')->find($id);
		// if($skill === null){
			// throw new NotFoundHttpException("La compétence d'id ".$id." n'existe pas. ");
		// }
		
		// On creér le formBuilder grâce au service form factory
		$form = $this->get('form.factory')->create(SkillType::class, $skill);
	
		if($request->isMethod('POST') && $form->handleRequest($request)->isValid()){
			// On récupère l'EntityManager
			$em = $this->get('doctrine')->getManager();	
			//$em->persist($advert);
			$em->flush();
			$request->getSession()->getFlashBag()->add('notice', 'compétence bien modifiée.');
			return $this->redirectToRoute('oc_platform_view_skill', array('id' => $skill->getId()));
		}
		
		return $this->render('OCPlatformBundle:Skill:edit.html.twig', array('skill' => $skill , 'form' =>  $form->createView()));

    }
	
	
	public function viewAction(Request $request, Skill $skill) 
	{
		$doctrine = $this->get('doctrine');
		// Get EM
		$em = $doctrine->getManager();
		return $this->render('OCPlatformBundle:Skill:view.html.twig', array(
			'skill' => $skill, 
		));
    }
	
}
