<?php

namespace OC\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use OC\UserBundle\EventListener\RegistrationListener;



class RegistrationType extends AbstractType
{
	/**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder->add('nom')
				->add('prenom')
				->add('mode', ChoiceType::class, array(
							'choices'  => array(
								'Recruteur' => 'recruteur',
								'Candidat' => 'candidat'
							)));
		// On ajoute une fonction qui va écouter un événement 
		$builder->addEventSubscriber(new RegistrationListener());							
		
	}
	
	public function getParent()
    {
		return 'FOS\UserBundle\Form\Type\RegistrationFormType';
	}
	 
	 
	public function getBlockPrefix()
    {
       return 'oc_user_registration';
    }
 
}
