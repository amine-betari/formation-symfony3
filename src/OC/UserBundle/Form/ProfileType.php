<?php

namespace OC\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use OC\UserBundle\EventListener\ProfileListener;



class ProfileType extends AbstractType
{

	private $service_container;

  
	public function __construct($service_container)
	{
		$this->service_container = $service_container;
	}
	
	
	/**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder->add('nom')
				->add('prenom')
				->add('cv', FileType::class, array(
					'data_class' => null,
					'label' => 'CV (PDF file)',
					'required' => false ));

		$builder->addEventSubscriber(new ProfileListener($this->service_container));							
	}
	
	/**
     *
     */
    public function getParent()
    {
		return 'FOS\UserBundle\Form\Type\ProfileFormType';
    }
	 
	 
	public function getBlockPrefix()
    {
       return 'os_user_profile';
    }

}
