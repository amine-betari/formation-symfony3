<?php

namespace OC\PlatformBundle\EventListener;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\File\FileUploader;
use OC\PlatformBundle\Email\ApplicationMailer;
use Symfony\Bundle\TwigBundle\TwigEngine;

class ApplicationCreationListener
{
	private $uploader;

	private $mailer;
	
	private $tokenStorage;
	
	
	public function __construct(FileUploader $uploader,  ApplicationMailer $mailer, $tokenStorage) 
	{
		$this->uploader = $uploader;
		$this->mailer = $mailer;
		$this->tokenStorage = $tokenStorage;
		
	}

	public function postPersist(LifecycleEventArgs $args) 
	{
		$entity = $args->getEntity();
		if (! $entity instanceof Application )  return;
		$this->mailer->sendNewNotification($entity);
	}
	public function prePersist(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();
		$this->uploadFile($entity);
	}

	public function preUpdate(PreUpdateEventArgs $args)
	{
		$entity = $args->getEntity();
		$this->uploadFile($entity);
	}

	public function postRemove(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();
		$this->removeFile($entity);
	}

	public function removeFile($entity)
	{
		if (!$entity instanceof Application) {
			return;
		}
		$file = $entity->getBrochure();
		if( $entity->getAuthor()->getCv() !== $entity->getBrochure() ) {
			// delete file from faillure
			$pathFile = $this->uploader->getTargetDir().'/'.$file;
			if(file_exists($pathFile)) unlink($pathFile);
		} 
	}
	

	public function uploadFile($entity)
	{
		if (!$entity instanceof Application) {
			return;
		}
		
		// cas : internaute a son CV et n'a rien uploader
		if( $this->tokenStorage->getToken()->getUser()->getCv() != null && $entity->getBrochure() == null) {
			$fileName = $this->tokenStorage->getToken()->getUser()->getCv();
		} else {
			$file = $entity->getBrochure();
				// only upload new filess
			if (!$file instanceof UploadedFile) return;
			$fileName = $this->uploader->upload($file);
		}
        $entity->setBrochure($fileName);
	}
}
