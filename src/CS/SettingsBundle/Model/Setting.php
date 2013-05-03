<?php

/*
 * This file is part of the CSSettingsBundle package.
 *
 * (c) Pierre du Plessis <info@customscripts.co.za>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CS\SettingsBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Setting
 * @package CS\SettingsBundle\Model
 *
 * @ORM\MappedSuperclass
 */
class Setting
{
    /**
     * @ORM\Column(name="`key`", type="string", length=125, nullable=false)
     */
    protected $key;

    /**
     * @ORM\Column(name="`value`", type="text", nullable=true)
     */
    protected $value;

    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(name="field_type", type="string", nullable=true)
     */
    protected $type;

    /**
     * @ORM\Column(name="field_options", type="array", nullable=true)
     */
    protected $options;

    /**
     * @ORM\ManytoOne(targetEntity="Section", inversedBy="settings")
     */
    protected $section;

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
     * @param  Section $section
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
     * @param  string  $type
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
