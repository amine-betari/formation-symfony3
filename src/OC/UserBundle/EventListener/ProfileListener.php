<?php 
namespace OC\UserBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

 
class ProfileListener implements EventSubscriberInterface {

	public function __construct($service_container) 
	{
		$this->service_container = $service_container;
	}
	public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_SUBMIT => 'onPostSubmit',
        );
    }
	
	public function onPostSubmit(FormEvent $event)
    {
		
        $user = $event->getData();
        /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
		$file = $user->getCv();
		
		//oc_platform.brochure_uploader
		$serviceFile = $this->service_container->get('oc_platform.brochure_uploader');
		$fileName = $serviceFile->upload($file);

        $user->setCv($fileName);
	
        $form = $event->getForm();

        if (!$user) {
            return;
        }
    }
}