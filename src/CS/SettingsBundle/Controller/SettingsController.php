<?php

/*
 * This file is part of the CSSettingsBundle package.
 *
 * (c) Pierre du Plessis <info@customscripts.co.za>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CS\SettingsBundle\Controller;

use CS\CoreBundle\Controller\Controller;
use CS\SettingsBundle\Form\Type\SettingsType;

class SettingsController extends Controller
{
    /**
     * Settings action
     */
    public function indexAction()
    {
        $settingsRepository = $this->getRepository('CSSettingsBundle:Setting');
        $sections = $settingsRepository->getSections(); //array('general', 'quotes', 'invoices', 'email', 'currency', 'Cron');

        $settings = $settingsRepository->getAllSettings();

        $form = $this->createForm(new SettingsType($this->getEm()), $settings);

        return $this->render('CSSettingsBundle:Settings:index.html.twig', array('sections' => $sections, 'form' => $form->createView()));
    }
}
