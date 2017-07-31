<?php

namespace OC\PlatformBundle\EventListener;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\File\FileUploader;
use OC\PlatformBundle\Email\ApplicationMailer;

class BrochureUploadListener
{
	private $uploader;

	public function __construct(FileUploader $uploader) 
	{
		$this->uploader = $uploader;
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
		// delete file from faillure
		$pathFile = $this->uploader->getTargetDir().'/'.$file;
		if(file_exists($pathFile)) unlink($pathFile);
	}

	public function uploadFile($entity)
	{
		if (!$entity instanceof Application) {
			return;
		}
		 $file = $entity->getBrochure();
	        // only upload new filess
                if (!$file instanceof UploadedFile) {
        		return;
                }

                $fileName = $this->uploader->upload($file);
                $entity->setBrochure($fileName);
	}
}
