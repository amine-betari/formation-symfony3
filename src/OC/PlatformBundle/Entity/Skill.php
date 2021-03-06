<?php

namespace OC\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Skill
 *
 * @ORM\Table(name="oc_skill")
 * @ORM\Entity(repositoryClass="OC\PlatformBundle\Repository\SkillRepository")
 */
class Skill
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
     * @ORM\Column(name="name", type="string", length=255)
	 * 
     */
    private $name;
	
	/**
     * @ORM\OneToMany(targetEntity="OC\PlatformBundle\Entity\AdvertSkill", mappedBy="skill", orphanRemoval=true, cascade={"all"} )
	 */
	private $advertskilles;
	 


    public function getPhone()
    {
	return $this->getName();
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
     * Set name
     *
     * @param string $name
     *
     * @return Skill
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->advertskilles = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add advertskille
     *
     * @param \OC\PlatformBundle\Entity\AdvertSkill $advertskille
     *
     * @return Skill
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
}
