<?php

namespace OC\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class ApplicationType extends AbstractType
{
	private $security;
	
	public function __construct($security)
	{
		$this->security = $security;
	}
	
	
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		if( $options['user'] ) {
			$builder
			->add('content', TextareaType::class, array('label' => 'Exprimez-vous'))
			->add('brochure', FileType::class, array('label' => 'CV (PDF file)', 'required' => false))
			->add('save', SubmitType::class);
			
			
			 $builder->addEventListener(
				FormEvents::POST_SUBMIT, function (FormEvent $event) {
					$application = $event->getData();
					$form = $event->getForm();
					if ( $this->security->getToken()->getUser()->getCv() != null && $application->getBrochure() == null ) {
						$event->stopPropagation();
					}
				}, 900);
		} 
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OC\PlatformBundle\Entity\Application',
			'user' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'oc_platformbundle_application';
    }


}
