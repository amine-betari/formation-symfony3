<?php

namespace OC\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Image
 *
 * @ORM\Table(name="oc_image")
 * @ORM\Entity(repositoryClass="OC\PlatformBundle\Repository\ImageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Image
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255)
     */
    private $alt;
	
	private $file;
	
	// On ajoute cet attribut pour y stocker le nom du fichier temporairement
	private $tempFilename;
	
	public function getUploadDir()
	{
		// On retourne  le chemin relatif vers l'image pour un navigateur (relatif au répéertoire /web )
		return 'uploads/img';
	}
	
	public function getUploadRootDir()
	{
		// On retourne le chemin relatif vers l'image pour notre code
		return __DIR__.'/../../../../web/'.$this->getUploadDir();
	}
	
	public function getFile() 
	{	
		return $this->file;
	}
	
	
	public function setFile(UploadedFile $file = null)
	{
		$this->file = $file;
		// On vérifie si on avait déja un fichier pour cette entité
		if (null !== $this->url) {
			$this->tempFilename = $this->url;
			
			$this->url = null; 
			$this->alt = null;
		}
	}
	
	
	/**
	 * @ORM\PrePersist()
	 * @ORM\PreUpdate()
	 */
	public function preUpload()
	{
		if(null === $this->file) return;
		$this->url = $this->file->guessExtension();
		$this->alt = $this->file->getClientOriginalName();
	}
	
	
	/**
	 * @ORM\PostPersist()
	 * @ORM\PostUpdate()
	 */
	public function upload()
	{
		if(null === $this->file) return;
		// Si on avait un ancien fichier, on le supprime
		if(null !== $this->tempFilename) {
			$oldFile = $this->getUploadRootDir().'/'.$this->id.'.'.$this->tempFilename;
			if(file_exists($oldFile)) {
				unlink($oldFile);
			}
		}
		// On déplace le fichier envoyé dans le répértoire de notre choix
		$this->file->move(
			$this->getUploadRootDir(), // le répertoire de destination
			$this->id.'.'.$this->url   // le nom de fichier à créer, "id.extension"
		);
	}
	
	
	/**
	 * @ORM\PreRemove()
	 */
	public function preRemoveUpload()
	{
		// On saouvegarde temporairement le nom du fichier, car il dépend de l'id
		$this->tempFilename = $this->getUploadRootDir().'/'.$this->id.'.'.$this->url;
	}
	
	/**
	 * @ORM\PostRemove()
	 */
	public function removeUpload()
	{
		// En PostName, on n'a pas accés à l'id, on utilise notre nom sauvegardé
		if(file_exists($this->tempFilename)) {
			// On supprime le fichier
			unlink($this->tempFilename);
		}
	}
	
	
	public function getWebPath() 
	{
		return $this->getUploadDir().'/'.$this->getId().'.'.$this->getUrl();
	}
	
	
	
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Image
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set alt
     *
     * @param string $alt
     *
     * @return Image
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }


    public function __toString()
    {
	return $this->alt; // <-- add here a real property which
    }
}
