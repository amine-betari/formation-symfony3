<?php

namespace OC\PlatformBundle\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AntifloodValidator extends ConstraintValidator
{
	private $requestStack;
	private $em;
	
	// Les arguments declares dans la definition du service arrivent au contrucuteur
	// On doit les enregistrer dans l'objet pour pouvoir s'en resservir dans la methode validate()
	public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
	{
		$this->requestStack = $requestStack;
		$this->em 			=  $em;
	}
	
	
	public function validate($value, Constraint $constraint)
	{
	
	}
}
