<?php

/*
 * This file is part of the CSSettingsBundle package.
 *
 * (c) Pierre du Plessis <info@customscripts.co.za>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CS\SettingsBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class SectionRepository
 * @package CS\SettingsBundle\Repository
 */
class SectionRepository extends EntityRepository
{

    /**
     * Returns an array of all the top-level sections
     *
     * @param  bool   $cache
     * @param  string $cacheKey
     * @param  int    $lifetime
     * @return array
     */
    public function getTopLevelSections($cache = false, $cacheKey = 'cs_settings_top_section_sections', $lifetime = 604800)
    {
        $qb = $this->createQueryBuilder('s')
                   ->where('s.parent IS NULL');

        $query = $qb->getQuery();

        if ($cache) {
            $query->useResultCache(true, $lifetime, $cacheKey);
        }

       return $query->getResult();
    }
}
