<?php

namespace CS\SettingsBundle\Repository;

use Doctrine\ORM\EntityRepository;

class SectionRepository extends EntityRepository
{
    /**
     * Returns an array of all the top-level sections
     *
     * @return array
     */
    public function getTopLevelSections($cache = true, $cacheKey = 'cs_settings_top_section_sections', $lifetime = 604800)
    {
        $qb = $this->createQueryBuilder('s')
                   ->where('s.parent IS NULL');

        $query = $qb->getQuery();

        if($cache) {
            $query->useResultCache(true, $lifetime, $cacheKey);
        }

       return $query->getResult();
    }
}
