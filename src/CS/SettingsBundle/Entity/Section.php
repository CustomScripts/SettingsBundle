<?php

namespace CS\SettingsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="config_sections")
 * @ORM\Entity(repositoryClass="CS\SettingsBundle\Repository\SectionRepository")
 * @UniqueEntity("name")
 */
class Section
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=125, nullable=false, unique=true)
     */
    private $name;

    /**
    * @ORM\ManyToOne(targetEntity="Section", inversedBy="children")
    */
    private $parent;

    /**
    * @ORM\OneToMany(targetEntity="Section", mappedBy="parent")
    */
    private $children;

    /**
     * @ORM\OneToMany(targetEntity="Setting", mappedBy="section")
     * @var ArrayCollection
     */
    private $settings;

    public function __construct()
    {
        $this->children = new ArrayCollection;
        $this->settings = new ArrayCollection;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
     * Set name
     *
     * @param  string  $name
     * @return Section
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get parent
     *
     * @return Section
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set parent
     *
     * @param self $parent
     * @return Section
     */
    public function setParent(self $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get children
     *
     * @return ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Add child
     *
     * @param self $child
     * @return Section
     */
    public function addChild(self $child)
    {
        $this->children[] = $child;
        $child->setParent($this);

        return $this;
    }

    /**
     * Remove child
     *
     * @param self $child
     * @return Section
     */
    public function removeChild(self $child)
    {
        $this->children->removeElement($child);

        return $this;
    }

    /**
     * Get settings
     *
     * @return ArrayCollection
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Add a setting
     * @param Setting $setting
     * @return Section
     */
    public function addSetting(Setting $setting)
    {
        $this->settings[] = $setting;
        $setting->setSection($this);

        return $this;
    }

    /**
     * Remove a setting
     *
     * @param Setting $setting
     * @return Section
     */
    public function removeSetting(Setting $setting)
    {
        $this->settings->removeElement($setting);

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}