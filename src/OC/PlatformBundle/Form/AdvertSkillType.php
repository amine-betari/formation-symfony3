<?php

namespace OC\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class AdvertSkillType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('level', ChoiceType::class, array(
		'choices' => array(
			'Expert'   => 'expert',
			'Debutant' => 'debutant',
			'Senior'   => 'senior'
		)
	    ))
//            ->add('advert')
//            ->add('skill')
            ->add('skill', EntityType::class, array(
                                 'class'         => 'OCPlatformBundle:Skill',
                                 'choice_label'  => 'name',
                                 'multiple'      => false,  
                           ))

        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OC\PlatformBundle\Entity\AdvertSkill'
        ));
    }
}
