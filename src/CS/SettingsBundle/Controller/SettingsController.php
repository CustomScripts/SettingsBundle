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
        $manager = $this->get('settings');

        $sections = $manager->getSections();
        $settings = $manager->getSettings()->toArray();

        $form = $this->createForm(new SettingsType($manager), $settings);

        $request = $this->getRequest();

        if($request->isMethod('POST')) {
            $form->bind($request);

            $manager->setArray($request->request->get('settings'));

            $em = $this->getEm();
            $em->flush();

            $this->flash($this->trans('Settings saved successfully!'));

            return $this->redirect($this->generateUrl($request->get('_route')));
        }

        return $this->render('CSSettingsBundle:Settings:index.html.twig', array('sections' => $sections, 'form' => $form->createView()));
    }
}
