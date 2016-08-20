<?php

namespace OC\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AdvertType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date',  DateType::class, [
					'widget' => 'single_text',
					'format' => 'dd-MM-yyyy',
					'attr' => [
						'class' => 'form-control input-inline datepicker',
						'data-provide' => 'datepicker',
				        'data-date-format' => 'dd-mm-yyyy'
					]
				] )
			  ->add('title',     TextType::class)
			  ->add('content',   TextareaType::class)
			  ->add('author',    TextType::class)
			  ->add('published', CheckboxType::class, array('required' => false))
			  ->add('save',      SubmitType::class);
            
			
			/*->add('updatedAt', 'datetime')
            ->add('nbApplications')
            ->add('slug')
            ->add('categories')
            ->add('image')*/
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OC\PlatformBundle\Entity\Advert'
        ));
    }
}
