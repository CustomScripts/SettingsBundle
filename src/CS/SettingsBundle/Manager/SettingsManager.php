<?php

namespace CS\SettingsBundle\Manager;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Collection;
use CS\SettingsBundle\Collection\SettingsCollection;
use CS\SettingsBundle\Entity\Section;

class SettingsManager
{
    protected $accessor;

    protected $settings;

    protected $sections;

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
    public function get($setting)
    {
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
}
