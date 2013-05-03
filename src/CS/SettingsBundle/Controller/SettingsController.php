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
use CS\SettingsBundle\Model\Setting;

/**
 * Class SettingsController
 * @package CS\SettingsBundle\Controller
 */
class SettingsController extends Controller
{
    /**
     * Settings action
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        /** @var \CS\SettingsBundle\Manager\SettingsManager $manager */
        $manager = $this->get('settings');

        $settings = $manager->getSettings()->toArray();

        array_walk_recursive($settings, function(Setting &$setting){
            $setting = $setting->getValue();
        });

        $form = $this->createForm(new SettingsType(), $settings, array('manager' => $manager));

        $request = $this->getRequest();

        if ($request->isMethod('POST')) {
            $form->bind($request);

            $manager->set($request->request->get('settings'));

            $this->flash($this->trans('Settings saved successfully!'));

            return $this->redirect($this->generateUrl($request->get('_route')));
        }

        return $this->render('CSSettingsBundle:Settings:index.html.twig', array('settings' => $settings, 'form' => $form->createView()));
    }
}
