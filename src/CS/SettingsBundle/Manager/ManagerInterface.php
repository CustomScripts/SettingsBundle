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

use CS\SettingsBundle\Loader\SettingsLoaderInterface;
use Zend\Config\Config;

interface ManagerInterface
{
    /**
     * @param  SettingsLoaderInterface      $loader
     * @return SettingsLoaderInterface|void
     */
    public function addSettingsLoader(SettingsLoaderInterface $loader);

    /**
     * @param  string|null                                          $setting
     * @return Collection|mixed|string
     * @throws \CS\SettingsBundle\Exception\InvalidSettingException
     */
    public function get($setting = null);

    /**
     * Recursively set settings from an array
     *
     * @param  array      $settings
     * @return mixed|void
     */
    public function set(array $settings = array());

    /**
     * @return mixed|Config
     */
    public function getSettings();
}
