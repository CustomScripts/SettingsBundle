<?php

/*
 * This file is part of the CSSettingsBundle package.
 *
 * (c) Pierre du Plessis <info@customscripts.co.za>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CS\SettingsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Form;
use Doctrine\Common\Collections\ArrayCollection;

class Settings extends AbstractType
{
    protected $settings;

    public function __construct($settings)
    {
        $this->settings = $settings;
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach($this->settings as $key => $setting) {
            if($setting instanceof ArrayCollection) {
                $builder->add($key, new self($setting));
            } else {
                $options = array('help' => $setting->getDescription());
                $type = $setting->getType();

                $settingOptions = $setting->getOptions();

                if(!empty($settingOptions)) {
                    $options['choices'] = array_combine($settingOptions, $settingOptions);
                }

                if($setting->getType() === 'radio') {
                    $type = 'choice';
                    $options['expanded'] = true;
                    $options['multiple'] = false;
                }

                $builder->add($setting->getKey(), $type, $options);
            }
        }
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'settings';
    }
}
