<?php
// src/OC/PlatformBundle/Email/ApplicationMailer.php

namespace OC\PlatformBundle\Email;

use OC\PlatformBundle\Entity\Application;
use AmineBundle\Entity\Contact;
//use Symfony\Bundle\TwigBundle\TwigEngine;



class ApplicationMailer
{
  /**
   * @var \Swift_Mailer
   */
  private $mailer;
  
  private $email;

  //private $templating;
  private $service_container;

  public function __construct(\Swift_Mailer $mailer,  $email, $service_container)
  {
	$this->mailer = $mailer;
	$this->email = $email;
//	$this->templating = $templating;
	$this->service_container = $service_container;
  }

  public function sendNewNotification(Application $application)
  {
	if (!$application instanceof Application) {
			return;
		}
		// mail to admin
		$message = new \Swift_Message('Nouvelle candidature','Vous avez reçu une nouvelle candidature.');
		$message
		  ->setSubject('Nouvelle candidature pour le poste : '.$application->getAdvert()->getTitle())
		  ->setFrom($application->getAuthor()->getEmail())
		  //->setTo($this->service_container->getParameter('blogger_blog.emails.contact_email'))
		  ->setTo($this->email)
		  ->setBody($this->service_container->get('twig')->render('AmineBundle:Default:applicationEmailAdmin.html.twig', array('application' => $application)), 'text/html')
		;
		$this->mailer->send($message);

		// mail to internaute
                $message = new \Swift_Message('Nouvelle candidature','Vous avez reçu une nouvelle candidature.');
                $message
                  ->setSubject('Votre candidature pour le post '.$application->getAdvert()->getTitle())
                  ->setFrom($this->service_container->getParameter('blogger_blog.emails.contact_email'))
                  ->setTo($application->getAuthor()->getEmail())
                  ->setBody($this->service_container->get('twig')->render('AmineBundle:Default:applicationEmailInternaute.html.twig', array('application' => $application)))
                ;
                $this->mailer->send($message);

  }
  
  public function sendMailWebMaster(Contact $contact)
  {
	$message = new \Swift_Message('Nouveau contact','Vous avez reçu une nouvelle candidature.');
   $message
      ->setSubject($contact->getName())
      ->setFrom($contact->getEmail())
      ->setTo('amine.betari@gmail.com')
      ->setBody($this->service_container->get('twig')->render('AmineBundle:Default:contactEmail.txt.twig', array('contact' => $contact)))
    ;

    $this->mailer->send($message);
  }
  
}
