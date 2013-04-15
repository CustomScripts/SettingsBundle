<?php

namespace CS\SettingsBundle\Manager;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Collection;
use CS\SettingsBundle\Collection\SettingsCollection;
use CS\SettingsBundle\Entity\Section;
use CS\SettingsBundle\Entity\Setting;

class SettingsManager
{
    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessorInterface
     */
    protected $accessor;

    /**
     * @var Collection
     */
    protected $settings;

    /**
     * @var array
     */
    protected $sections;

    /**
     * @var ManagerRegistry
     */
    protected $em;

    CONST LEFT_TOKEN = '[';
    CONST RIGHT_TOKEN = ']';

    /**
     * Constructor
     *
     * @param EntityManager $em
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->em = $doctrine->getManager();

        $this->sections = $this->em->getRepository('CSSettingsBundle:Section')->getTopLevelSections();

        $this->settings = new SettingsCollection;

        if(count($this->sections) > 0) {
            foreach($this->sections as $section) {
                $this->settings[$section->getName()] = new SettingsCollection;

                $this->getSectionSettings($section, $this->settings[$section->getName()]);
            }
        }

        $this->accessor = PropertyAccess::getPropertyAccessor();
    }

    /**
     * Adds a section settings to the collection
     *
     * @param Section $section
     * @param Collection $collection
     * @return Collection
     */
    protected function getSectionSettings(Section $section, Collection $collection)
    {
        $settings = $this->em->getRepository('CSSettingsBundle:Setting')->getSettingsBySection($section, false);

        if(count($section->getChildren()) > 0) {
            foreach($section->getChildren() as $child) {
                $collection[$child->getName()] = new SettingsCollection;
                $this->getSectionSettings($child, $collection[$child->getName()]);
            }
        }

        if(is_array($settings) && !empty($settings)) {
            foreach($settings as $key => $value) {
                $collection[$value->getKey()] = $value;
            }
        }

        return $collection;
    }

    /**
     * Returns a setting value
     *
     * @param  string     $setting
     * @throws \Exception
     * @return mixed
     */
    public function get($setting = null)
    {
        if(empty($setting)) {
            return $this->getSettings();
        }

        if (strpos($setting, '.') !== false) {
            $split = array_filter(explode('.', $setting));

            if (!count($split) > 1) {
                throw new \Exception(sprintf('Invalid settings option: %s', $setting));
            }

            unset($setting);

            $setting = '';

            foreach ($split as $value) {

                if (strpos($value, self::LEFT_TOKEN) !== 0) {
                    $setting .= self::LEFT_TOKEN;
                }

                $setting .= $value;

                if (strrpos($value, self::RIGHT_TOKEN) !== strlen($value) - 1) {
                    $setting .= self::RIGHT_TOKEN;
                }
            }
        }

        if (strpos($setting, self::LEFT_TOKEN) !== 0) {
            $setting = self::LEFT_TOKEN . $setting;
        }

        if (strrpos($setting, self::RIGHT_TOKEN) !== strlen($setting) - 1) {
             $setting .= self::RIGHT_TOKEN;
        }

        return $this->accessor->getValue($this->settings, $setting);
    }

    /**
     * Get all the top-level sections
     *
     * @return array
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * Get the settings
     *
     * @return ArrayCollection
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Set a setting value
     *
     * @param string $path
     * @param mixed $value
     * @throws \Exception
     */
    public function set($path, $value)
    {
        $setting = $this->get($path);

        if($setting instanceof Setting) {
            $setting->setValue($value);
            $this->em->persist($setting);
        } else {
            throw new \Exception(sprintf('Invalid setting path: %s', $path));
        }
    }

    /**
     * Recursively set settings from an array
     *
     * @param array $settings
     * @param string|null $section
     */
    public function setArray(array $settings = array(), $section = null)
    {
        if(!empty($settings)) {
            foreach($settings as $key => $value) {
                $sectionPath = implode('.', array_filter(array($section, $key)));

                if(is_array($value)) {
                    $this->setArray($value, $sectionPath);
                } else {
                    $this->set($sectionPath, $value);
                }
            }
        }
    }
}
