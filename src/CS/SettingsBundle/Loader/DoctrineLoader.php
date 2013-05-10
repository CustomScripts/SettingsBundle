<?php

/*
 * This file is part of the CSSettingsBundle package.
 *
 * (c) Pierre du Plessis <info@customscripts.co.za>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CS\SettingsBundle\Loader;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use CS\SettingsBundle\Loader\SettingsLoaderInterface;
use Doctrine\DBAL\DBALException;

/**
 * Class DoctrineLoader
 *
 * This class loads settings from the database
 *
 * @package CS\SettingsBundle\Loader
 */
class DoctrineLoader implements SettingsLoaderInterface
{

    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @var array
     */
    protected $sections;

    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->manager = $doctrine->getManager();
    }

    /**
     * @param array $settings
     */
    public function saveSettings(array $settings = array())
    {
        $this->save($settings);
        $this->manager->flush();
    }

    /**
     * @param array $settings
     */
    protected function save(array $settings = array())
    {
        if (!empty($settings)) {
            foreach ($settings as $setting) {
                if (is_array($setting)) {
                    $this->saveSettings($setting);
                } else {
                    $this->manager->persist($setting);
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        if(!$this->checkConnection()) {
            return array();
        }

        return $this->addSettings($this->sections);
    }

    /**
     * @param array|\ArrayAccess $sections
     * @return array
     */
    protected function addSettings($sections)
    {
        $settings = array();

        /** @var \CS\SettingsBundle\Repository\SettingsRepository $repository */
        $repository = $this->manager->getRepository('CSSettingsBundle:Setting');

        foreach ($sections as $section) {
            /** @var \CS\SettingsBundle\Entity\Section $section */
            $values = $repository->getSettingsBySection($section, false);

            if (is_array($values) && !empty($values)) {
                foreach ($values as $value) {
                    /** @var \CS\SettingsBundle\Model\Setting $value */
                    $settings[$section->getName()][$value->getKey()] = $value;
                }
            }

            if (count($section->getChildren()) > 0) {
                $settings[$section->getName()] = $this->addSettings($section->getChildren());
            }
        }

        return $settings;
    }

    /**
     * Check if we can connect to the database and if the tables are loaded
     * @return bool
     */
    protected function checkConnection()
    {
        try {
            /** @var \CS\SettingsBundle\Repository\SectionRepository $repository */
            $repository = $this->manager->getRepository('CSSettingsBundle:Section');
            $this->sections = $repository->getTopLevelSections();
        } catch(DBALException $e) {
            return false;
        } catch (\PDOException $e) {
            return false;
        }

        return true;
    }
}
