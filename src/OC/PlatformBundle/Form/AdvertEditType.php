<?php

namespace OC\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
// use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;


class AdvertEditType extends AdvertType
{
	
//    public $securityContext;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$idCurrentAdvert = $builder->getData()->getId();
		$builder->remove("date");
		// add  a hidden input without have a filed in entity
		$builder->add('advertId', HiddenType::class, 
				array('mapped' => false ,
					  'data' => $idCurrentAdvert,
				));
    }
    
    
    /**
     *
     */
    public function getParent()
    {
    	return parent::class;
    }
}
