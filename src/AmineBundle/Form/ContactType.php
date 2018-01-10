<?php
// src/Blogger/BlogBundle/Form/EnquiryType.php

namespace AmineBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name',null, array('label' => 'Votre Nom'));
        $builder->add('email', EmailType::class, array('label' => 'Votre Email'));
        $builder->add('subject', TextType::class, array('label' => 'Sujet'));
        $builder->add('body', TextAreaType::class, array('label' => 'Message'));
    }

    public function getName()
    {
        return 'contact';
    }
}
