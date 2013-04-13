<?php

namespace CS\SettingsBundle\Manager;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Doctrine\Common\Persistence\ManagerRegistry;

class SettingsManager
{
    protected $accessor;

    CONST LEFT_TOKEN = '[';
    CONST RIGHT_TOKEN = ']';

    /**
     * Constructor
     *
     * @param EntityManager $em
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();

        $this->settings = $em->getRepository('CSSettingsBundle:Setting')->getAllSettings();

        $this->accessor = PropertyAccess::getPropertyAccessor();
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
