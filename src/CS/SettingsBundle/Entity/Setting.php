<?php

/*
 * This file is part of the CSSettingsBundle package.
 *
 * (c) Pierre du Plessis <info@customscripts.co.za>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CS\SettingsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use CS\SettingsBundle\Model\Setting as BaseClass;

/**
 * Class Setting
 * @package CS\SettingsBundle\Entity
 *
 * @ORM\Table(name="app_config")
 * @ORM\Entity(repositoryClass="CS\SettingsBundle\Repository\SettingsRepository")
 */
class Setting extends BaseClass
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
