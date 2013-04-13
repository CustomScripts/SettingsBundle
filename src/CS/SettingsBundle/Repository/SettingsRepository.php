<?php

namespace CS\SettingsBundle\Repository;

use Doctrine\ORM\EntityRepository;
use CS\CoreBundle\Util\ArrayUtil;

class SettingsRepository extends EntityRepository
{
    /**
     * Returns an array of all the settings
     *
     * @return array
     */
    public function getAllSettings()
    {
        $sections = $this->getSections();

        if (count($sections) > 0) {
            $settings = array();
            foreach ($sections as $section) {
                $settings[$section] = $this->getSettingsBySection($section);
            }
        }

        return $settings;
    }

    /**
     * Gets section specific settings
     *
     * @param  string $section
     * @param  bool   $combineArray Should the settings be returned as a key => value array
     * @return array
     */
    public function getSettingsBySection($section, $combineArray = true)
    {
        $qb = $this->createQueryBuilder('s')
                    //->select('s.key, s.value')
                    ->where('s.section = :section')
                    ->orderBy('s.key', 'ASC')
                    ->setParameter('section', $section);

        $query = $qb->getQuery()
                    ->useQueryCache(true)
                    ->useResultCache(true, (60 * 60 * 24 * 7), 'app_config_section['.$section.']'); // we cache the config result, as the cache is cleared as soon as the config settings is changed

        $result = $query->getResult();

        if ($combineArray) {
            return array_combine(ArrayUtil::column($result, 'key'), ArrayUtil::column($result, 'value'));
        }

        return $result;
    }

    /**
     * Gets an array of all the available sections
     *
     * @return array
     */
    public function getSections()
    {
        $qb = $this->createQueryBuilder('s')
                   ->select('DISTINCT(s.section)')
                   ->orderBy('s.key', 'asc');

        $query = $qb->getQuery()
                    ->useQueryCache(true)
                    ->useResultCache(true, (60 * 60 * 24 * 7), 'app_config_sections'); // cache the result for 1 week

        $results = $query->getArrayResult();

        return ArrayUtil::column($results, 'section');
    }
}
