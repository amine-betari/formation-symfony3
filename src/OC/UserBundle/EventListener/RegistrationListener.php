<?php 
namespace OC\UserBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

 
class RegistrationListener implements EventSubscriberInterface {

	
	public static function getSubscribedEvents()
    {
        return array(
            //FormEvents::PRE_SET_DATA => 'onPreSetData',
            FormEvents::POST_SUBMIT   => 'onPostSubmit',
        );
    }
	
	
	public function onPostSubmit(FormEvent $event)
    {
		
        $user = $event->getData();
        $form = $event->getForm();

        if (!$user) {
            return;
        }
		if ($user->getMode() == "recruteur") {
			$roles = array('ROLE_RECRUTEUR');
			$user->setRoles($roles);
		} else {
			$roles = array('ROLE_CANDIDAT');
			$user->setRoles($roles);
		}
    }
}