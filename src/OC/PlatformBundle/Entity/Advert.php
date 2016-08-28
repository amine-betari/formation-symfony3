<?php

namespace OC\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
// Validator
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use OC\PlatformBundle\Validator\Antiflood;

/**
 * Advert
 *
 * @ORM\Table(name="oc_advert")
 * @ORM\Entity(repositoryClass="OC\PlatformBundle\Repository\AdvertRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields="title", message="Une annonce existe déjà avec ce titre.")
 */
class Advert
{
	
	/**
	 * @ORM\OneToMany(targetEntity="OC\PlatformBundle\Entity\AdvertSkill", mappedBy="advert", orphanRemoval=true, cascade={"all"} )
	 *
	 */
	private $advertskilles; 
	/**
	 * @ORM\OneToMany(targetEntity="OC\PlatformBundle\Entity\Application", mappedBy="advert", orphanRemoval=true, cascade={"all"} )
	 */
	private $applications;
	/**
	 * @ORM\ManyToMany(targetEntity="OC\PlatformBundle\Entity\Category", cascade={"persist"})
	 * @ORM\JoinTable(name="oc_advert_category")
	 */
	private $categories; 
	/**
	 * @ORM\OneToOne(targetEntity="OC\PlatformBundle\Entity\Image", cascade={"persist", "remove"})
	 * @Assert\Valid()
	 */
	private $image;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="published", type="boolean")
     * 
     */
    private $published = true;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
	 * @Assert\DateTime()
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, unique=true)
	 * @Assert\Length(min=10, minMessage="Le titre doit faire au moins {{ limit }} caractères.")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255)
	 * @Assert\Length(min=5)
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", length=255)
	 * @Assert\NotBlank()
	 * @Antiflood()
     */
    private $content;

	/**
	 * @ORM\Column(name="updated_at", type="datetime", nullable=true)
	 */
	private $updatedAt;
	
	/**
	 * @ORM\Column(name="nb_applications", type="integer")
	 */
	private $nbApplications = 0; 
	
	/**
	 * @Gedmo\Slug(fields={"title"})
	 * @ORM\Column(name="slug", type="string", length=255)
	 */
	private $slug; 
	
	public function __construct() {
		// par défaut, la date de l'annonce est la date d'aujourd'hui
		$this->date = new \Datetime();
		$this->categories = new ArrayCollection();
		$this->applications = new ArrayCollection();
		$this->advertskilles = new ArrayCollection();
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Advert
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Advert
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set author
     *
     * @param string $author
     *
     * @return Advert
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Advert
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set published
     *
     * @param boolean $published
     *
     * @return Advert
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set image
     *
     * @param \OC\PlatformBundle\Entity\Image $image
     *
     * @return Advert
     */
    public function setImage(\OC\PlatformBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \OC\PlatformBundle\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Add category
     *
     * @param \OC\PlatformBundle\Entity\Category $category
     *
     * @return Advert
     */
    public function addCategory(\OC\PlatformBundle\Entity\Category $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \OC\PlatformBundle\Entity\Category $category
     */
    public function removeCategory(\OC\PlatformBundle\Entity\Category $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add application
     *
     * @param \OC\PlatformBundle\Entity\Application $application
     *
     * @return Advert
     */
    public function addApplication(\OC\PlatformBundle\Entity\Application $application)
    {
        $this->applications[] = $application;

        return $this;
    }

    /**
     * Remove application
     *
     * @param \OC\PlatformBundle\Entity\Application $application
     */
    public function removeApplication(\OC\PlatformBundle\Entity\Application $application)
    {
        $this->applications->removeElement($application);
    }

    /**
     * Get applications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Advert
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
	
	
	/**
	 * @ORM\PreUpdate
	 */
	public function updateDate(){
		//exit;
		$this->setUpdatedAt(new \Datetime());
	}

    /**
     * Set nbApplications
     *
     * @param integer $nbApplications
     *
     * @return Advert
     */
    public function setNbApplications($nbApplications)
    {
        $this->nbApplications = $nbApplications;

        return $this;
    }

    /**
     * Get nbApplications
     *
     * @return integer
     */
    public function getNbApplications()
    {
        return $this->nbApplications;
    }
	
	
	public function increaseApplication(){
		$this->nbApplications++;
	}
	
	
	public function decreaseApplication(){
		$this->nbApplications--;
	}

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Advert
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Add advertskille
     *
     * @param \OC\PlatformBundle\Entity\AdvertSkill $advertskille
     *
     * @return Advert
     */
    public function addAdvertskille(\OC\PlatformBundle\Entity\AdvertSkill $advertskille)
    {
        $this->advertskilles[] = $advertskille;

        return $this;
    }

    /**
     * Remove advertskille
     *
     * @param \OC\PlatformBundle\Entity\AdvertSkill $advertskille
     */
    public function removeAdvertskille(\OC\PlatformBundle\Entity\AdvertSkill $advertskille)
    {
        $this->advertskilles->removeElement($advertskille);
    }

    /**
     * Get advertskilles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAdvertskilles()
    {
        return $this->advertskilles;
    }
	
	
	/**
	 * @Assert\Callback
	 */
	public function isContentValid(ExecutionContextInterface $context) 
	{
		$forbiddenWords = array('démotivation', 'abandon');

		// On vérifie que le contenu ne contient pas l'un des mots
		if (preg_match('#'.implode('|', $forbiddenWords).'#', $this->getContent())) {
		  // La règle est violée, on définit l'erreur
		  $context
			->buildViolation('Contenu invalide car il contient un mot interdit.') // message
			->atPath('content')                                                   // attribut de l'objet qui est violé
			->addViolation() // ceci déclenche l'erreur, ne l'oubliez pas
		  ;
		}
	}
}
