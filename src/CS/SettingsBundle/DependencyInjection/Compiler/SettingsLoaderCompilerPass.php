<?php

/*
 * This file is part of the CSSettingsBundle package.
 *
 * (c) Pierre du Plessis <info@customscripts.co.za>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CS\SettingsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class SettingsLoaderCompilerPass
 * @package CS\SettingsBundle\DependencyInjection\Compiler
 */
class SettingsLoaderCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('cs_settings.manager')) {
            return;
        }

        $services = $container->findTaggedServiceIds('settings.loader');

        if (count($services) > 0) {
            foreach ($services as $id => $parameters) {
                $container->getDefinition('cs_settings.manager')->addMethodCall('addSettingsLoader', array(new Reference($id)));
            }
        }
    }
}
