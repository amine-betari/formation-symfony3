<?php

namespace OC\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use OC\PlatformBundle\Form\AdvertSkillType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use OC\PlatformBundle\Repository\CategoryRepository;
use OC\PlatformBundle\Form\CkeditorType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class AdvertType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	$pattern = 'I%';
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
			  ->add('descriptif',   TextareaType::class)
			  ->add('profil',   TextareaType::class)
//			  ->add('author',    EntityType::class, array(
//						'class'         => 'OCUserBundle:User',
//						'data' => $options['user'],
//						'multiple'      => false,
//					))
			  ->add('salaire',     TextType::class)
			  ->add('ville',     TextType::class)
			  //->add('image', ImageType::class, array('required' => false)) // ImageType est un formulaire
                  	  ->add('categories', CollectionType::class, array(
						  'entry_type'   => CategoryType::class,
						  'allow_add'    => true,
						  'allow_delete' => true,
						  'allow_extra_fields'	 => true
					    ))
			   ->add('advertskilles', CollectionType::class, array(
					'entry_type'			=> AdvertSkillType::class,
					'allow_add'    => true,
					'allow_delete' => true,
					'by_reference' => false,
			   ))
			     // ->add('categories', EntityType::class, array(
					 // 'class'         => 'OCPlatformBundle:Category',
					 // 'choice_label'  => 'name',
					 // 'multiple'      => true,
					 // 'expanded'	 => true
							// /* 'query_builder' => function(CategoryRepository $repository) use($pattern) {
							// return $repository->getLikeQueryBuilder($pattern);
							 // }*/
				 // ))
			  ->add('save',      SubmitType::class);
	
		// On ajoute une fonction qui va écouter un événement 
		$builder->addEventListener(
			FormEvents::PRE_SET_DATA, // 1er argument : l'evenement qui nous interesse,
			function(FormEvent $event) { 
				// On recupere notre objet Advert sous-jacent
				$advert = $event->getData();
				if($advert === null) {
					return;
				}
				
				if(!$advert->getPublished() || null === $advert->getId()) {
					$event->getForm()->add('published', CheckboxType::class, array(
							'required' => false,
							'label' => ' Publier automatiquement l\'annonce à sa création'));
				} else {
					// Sinon on le supprime, 
					$event->getForm()->remove('published');
				}
			}
		);
		// 2eme evenement, setter current user
		$builder->addEventListener(
			FormEvents::SUBMIT,
			function(FormEvent $event) {
				 $advert = $event->getData();
				if($advert === null) {
						return;
				}
				$options = $event->getForm()->getConfig()->getOptions();
				$user =  $options['user'];
				$advert->setAuthor($user); 
			}
		);
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OC\PlatformBundle\Entity\Advert',
	    'user' => null,
        ));
    }
}

