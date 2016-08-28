<?php

namespace OC\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;



class AdvertEditType extends AdvertType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder->remove("date");
		//$builder->remove("categories");
    }
    
    public function getParent() 
	{
		return parent::class;
	}
}
