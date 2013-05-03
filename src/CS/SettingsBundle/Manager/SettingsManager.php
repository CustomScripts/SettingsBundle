<?php

/*
 * This file is part of the CSSettingsBundle package.
 *
 * (c) Pierre du Plessis <info@customscripts.co.za>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CS\SettingsBundle\Manager;

use CS\SettingsBundle\Collection\ConfigCollection;
use CS\SettingsBundle\Loader\SettingsLoaderInterface;
use CS\SettingsBundle\Exception\InvalidSettingException;
use CS\SettingsBundle\Model\Setting;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Doctrine\Common\Persistence\ManagerRegistry;
use Zend\Config\Config;

/**
 * Class SettingsManager
 * @package CS\SettingsBundle\Manager
 */
class SettingsManager implements ManagerInterface
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

    /**
     * @var ConfigCollection
     */
    protected $collection;

    /**
     * @var bool
     */
    protected $initialized;

    /**
     * @var array
     */
    protected $loaders = array();

    CONST LEFT_TOKEN = '[';
    CONST RIGHT_TOKEN = ']';

    /**
     * Constructor
     *
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->initialized = false;

        $this->em = $doctrine->getManager();

        $this->settings = new Config(array());

        $this->accessor = PropertyAccess::getPropertyAccessor();
    }

    /**
     * Initializes the loaders and load the default settings
     */
    protected function initialize()
    {
        $this->initialized = true;

        $this->collection = new ConfigCollection($this);

        foreach ($this->loaders as $loader) {

            /** @var SettingsLoaderInterface $loader */
            $this->collection->startSection(get_class($loader));

                $settings = new Config($loader->getSettings());

                $this->collection->add($settings);

                $this->settings->merge($settings);

            $this->collection->endSection();
        }
    }

    /**
     * @param  SettingsLoaderInterface      $loader
     * @return SettingsLoaderInterface|void
     */
    public function addSettingsLoader(SettingsLoaderInterface $loader)
    {
        $this->loaders[get_class($loader)] = $loader;
    }

    /**
     * @param  string|null                                          $setting
     * @return mixed|string
     * @throws \CS\SettingsBundle\Exception\InvalidSettingException
     */
    public function get($setting = null)
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        if (empty($setting)) {
            return $this->getSettings();
        }

        if (false !== strpos($setting, '.')) {
            $split = array_filter(explode('.', $setting));

            if (!count($split) > 1) {
                throw new InvalidSettingException($setting);
            }

            unset($setting);

            $setting = '';

            foreach ($split as $value) {

                if (0 !== strpos($value, self::LEFT_TOKEN)) {
                    $setting .= self::LEFT_TOKEN;
                }

                $setting .= $value;

                if (strrpos($value, self::RIGHT_TOKEN) !== strlen($value) - 1) {
                    $setting .= self::RIGHT_TOKEN;
                }
            }
        }

        if (0 !== strpos($setting, self::LEFT_TOKEN)) {
            $setting = self::LEFT_TOKEN . $setting;
        }

        if (strrpos($setting, self::RIGHT_TOKEN) !== strlen($setting) - 1) {
             $setting .= self::RIGHT_TOKEN;
        }

        $entity = $this->accessor->getValue($this->settings, $setting);

        if ($entity instanceof Setting) {
            return $entity->getValue();
        } else {
            return $entity;
        }
    }

    /**
     * @return mixed|Config
     */
    public function getSettings()
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return $this->settings;
    }

    /**
     * Recursively set settings from an array
     *
     * @param  array      $settings
     * @return mixed|void
     */
    public function set(array $settings = array())
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        if (!empty($settings)) {

            foreach ($this->collection->getSections() as $collectionSection) {

                $this->collection->startSection($collectionSection);

                $collectionSettings = array();

                foreach ($settings as $key => $value) {

                    $config = $this->collection->getSettings();

                    if (isset($config[$key])) {
                        $collectionSettings[$key] = $this->setData($config[$key], $value);
                    }
                }

                /** @var SettingsLoaderInterface $loader */
                $loader = $this->loaders[$collectionSection];
                $loader->saveSettings($collectionSettings);

                $this->collection->endSection();
            }
        }
    }

    /**
     * @param  Config $config
     * @param  array  $settings
     * @return array
     */
    protected function setData(Config $config, array $settings)
    {
        $settingsArray = array();

        foreach ($config as $section => $setting) {

            foreach ($settings as $key => $value) {
                if (is_array($value)) {
                    $settingsArray[$key] = $this->setData($setting, $value);
                } else {
                    if ($section === $key) {
                        /** @var \CS\SettingsBundle\Model\Setting $setting */
                        $setting->setValue($value);
                        $settingsArray[$key] = $setting;
                    }
                }
            }
        }

        return $settingsArray;
    }
}
