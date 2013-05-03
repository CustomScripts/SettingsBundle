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

/**
 * Interface SettingsLoaderInterface
 * @package CS\SettingsBundle\Loader
 */
interface SettingsLoaderInterface
{
    /**
     * Return an array with available settings
     * @return array
     */
    public function getSettings();

    /**
     * @param  array $settings
     * @return void
     */
    public function saveSettings(array $settings = array());
}
