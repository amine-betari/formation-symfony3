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
	
	// Les arguments d�clar�s dans la d�finition du service arrivent au contrucuteur
	// On doit les enregistrer dans l'objet pour pouvoir s'en resservir dans la m�thode validate()
	public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
	{
		$this->requestStack = $requestStack;
		$this->em 			=  $em;
	}
	
	
	public function validate($value, Constraint $constraint)
	{
		// Pour r�cup�rer l'objet Request tel qu'on le connait, il faut
		// utiliser getCurrentRequest du service request_stack
		$request = $this->requestStack->getCurrentRequest();
		// On r�cup�re l'IP de celui qui poste
		$ip = $request->getClientIp();
		
		// On v�rifie si cette IP a d�j� post� une candidature il y a moins de 15 secondes
		/*$isFlood = $this->em
			->getRepository('OCPlatformBundle:Application')
			->isFlood($ip, 15);
			
		if($isFlood) {
			$this->context->addViolation($constraint->message);
		}*/
	}
}
