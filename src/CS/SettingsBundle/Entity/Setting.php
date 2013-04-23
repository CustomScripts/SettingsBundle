<?php

namespace CS\SettingsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="app_config")
 * @ORM\Entity(repositoryClass="CS\SettingsBundle\Repository\SettingsRepository")
 */
class Setting
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="`key`", type="string", length=125, nullable=false)
     */
    private $key;

    /**
     * @ORM\Column(name="`value`", type="text", nullable=true)
     */
    private $value;

    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(name="field_type", type="string", nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(name="field_options", type="array", nullable=true)
     */
    private $options;

    /**
     * @ORM\ManytoOne(targetEntity="Section", inversedBy="settings")
     */
    private $section;

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
     * Get key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set key
     *
     * @param  string  $key
     * @return Setting
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set value
     *
     * @param  mixed   $value
     * @return Setting
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param  string  $description
     * @return Setting
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get section
     *
     * @return section
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * Set section
     *
     * @param Section $section
     * @return Setting
     */
    public function setSection(Section $section)
    {
        $this->section = $section;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Setting
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set type
     *
     * @param Array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    public function __toString()
    {
        return $this->value;
    }
}
