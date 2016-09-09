<?php

namespace OC\PlatformBundle\EventListener;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use OC\PlatformBundle\Beta\BetaHTMLAdder;


class BetaListener
{
  // Notre processeur
  protected $betaHTML;

  // La date de fin de la version bêta :
  // - Avant cette date, on affichera un compte à rebours (J-3 par exemple)
  // - Après cette date, on n'affichera plus le « bêta »
  protected $endDate;

  public function __construct(BetaHTMLAdder $betaHTML, $endDate)
  {
    $this->betaHTML = $betaHTML;
    $this->endDate  = new \Datetime($endDate);
  }

  // L'argument de la méthode est un FiltreResponseEvent
  public function processBeta(FilterResponseEvent $event)
  {
	// On test si la request est bien la requête principal (et non une sous-requête)
	if(!$event->isMasterRequest()) {
		return;
	}
	
	// On récupère la reponse que le gestionnaire a insérée dans l'évenement
	// $response = $event->getResponse();
	
	// Ici on modifie comme comme on veut la réponse
	
	// Puis on insère la réponse modifiée dans l'événement
	// $event->setResponse($response);
	
    $remainingDays = $this->endDate->diff(new \Datetime())->days;

    if ($remainingDays <= 0) {
      // Si la date est dépassée, on ne fait rien
      return;
    }
	
	// On utilise notre BetaHRML
    $response = $this->betaHTML->addBeta($event->getResponse(), $remainingDays);
    
    // On met à jour la réponse avec la nouvelle valeur
    $event->setResponse($response);
    
    // Ici on appelera la méthode
    // $this->betaHTML->addBeta()
	
    // On stoppe la propagation de l'évènement en cours (ici, kernel.response)
	 // $event->stopPropagation();

  }
}
