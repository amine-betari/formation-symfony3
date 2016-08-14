<?php
// src/OC/PlatformBundle/DoctrineListener/ApplicationCreationListener.php

namespace OC\PlatformBundle\DoctrineListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use OC\PlatformBundle\Email\ApplicationMailer;
use OC\PlatformBundle\Entity\Application;

class ApplicationCreationListener
{
  /**
   * @var ApplicationMailer
   */
  private $applicationMailer;

  public function __construct(ApplicationMailer $applicationMailer)
  {
    $this->applicationMailer = $applicationMailer;
	exit;
  }

  // la méthode doit respecter le même nom que l'événement
  public function postPersist(LifecycleEventArgs $args)
  {
    // l'objet LifecycleEventArgs offre deux méthodes : getObject et getObjectManager
    $entity = $args->getObject();

    // On ne veut envoyer un email que pour les entités Application
    if (!$entity instanceof Application) {
      return;
    }

    $this->applicationMailer->sendNewNotification($entity);
  }
}
