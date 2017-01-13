<?php

namespace OC\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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
		$builder->remove("date");
		//$builder->remove("categories");
    }
    
    
    /**
     *
     */
    public function getParent()
    {
    	return parent::class;
    }
}
