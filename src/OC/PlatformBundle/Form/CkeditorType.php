<?php

namespace OC\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionResolver;

class CkeditorType extends AbstractType
{
	public function configureOption(OptionsResolver $resolver)
	{
	  $resolver->setDefauls(array(
	    'attr' => array('class' => 'ckeditor')//On ajoute la classe CSS
	  ));
	}

	public function getParent()
	{
		return TextareaType::class;

	}
}
