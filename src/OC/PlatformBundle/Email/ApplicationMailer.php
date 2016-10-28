<?php
// src/OC/PlatformBundle/Email/ApplicationMailer.php

namespace OC\PlatformBundle\Email;

use OC\PlatformBundle\Entity\Application;
use AmineBundle\Entity\Contact;
use Symfony\Bundle\TwigBundle\TwigEngine;


class ApplicationMailer
{
  /**
   * @var \Swift_Mailer
   */
  private $mailer;
  /*
   * @var \EngineInterface
   */
  private $templating;
  /*
   * @var \Container
   */
  private $service_container;

  public function __construct(\Swift_Mailer $mailer, TwigEngine $templating, $service_container)
  {
    $this->mailer = $mailer;
	$this->templating = $templating;
	$this->service_container = $service_container;
  }

  public function sendNewNotification(Application $application)
  {
    $message = new \Swift_Message('Nouvelle candidature','Vous avez reçu une nouvelle candidature.');

    $message
      ->addTo('amine.betari@gmail.com') // Ici bien sûr il faudrait un attribut "email", j'utilise "author" à la place
      ->addFrom('admin@votresite.com')
    ;

    $this->mailer->send($message);
  }
  
  public function sendMailWebMaster(Contact $contact)
  {
	$message = new \Swift_Message('Nouveau contact','Vous avez reçu une nouvelle candidature.');
	
    $message
      ->setSubject($contact->getName())
      ->setFrom($contact->getEmail())
      ->setTo($this->service_container->getParameter('blogger_blog.emails.contact_email'))
	  //->setTo('amine.betari@gmail.com')
      ->setBody($this->templating->render('AmineBundle:Default:contactEmail.txt.twig', array('contact' => $contact)))
    ;

    $this->mailer->send($message);
  }
  
}
