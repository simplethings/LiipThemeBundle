<?php

/*
 * This file is part of the Liip/ThemeBundle
 *
 * (c) Lukas Kahwe Smith <smith@pooteeweet.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Liip\ThemeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Bundle\AsseticBundle\DependencyInjection\AsseticExtension;

class LiipThemeExtension extends Extension
{
    /**
     * Loads the services based on your application configuration.
     *
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->process($configuration->getConfigTree(), $configs);

        if (empty($config['themes']) || empty($config['activeTheme'])) {
            throw new \RuntimeException('Liip\ThemeBundle not completely configured please consult the README file.');
        }

        // Set parameters first, services depend on them.
        $container->setParameter($this->getAlias().'.themes', $config['themes']);
        $container->setParameter($this->getAlias().'.activeTheme', $config['activeTheme']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('templating.xml');
        
        if ($container->hasParameter('assetic.asset_factory.class')) {
            #$this->configureAssetic($container);
        }
    }
    
    private function configureAssetic($container)
    {
        $map = $container->getParameter('kernel.bundles');

        // bundle views/ directories and kernel overrides
        foreach ($map as $name => $class) {
            $rc = new \ReflectionClass($class);
            foreach (array('twig', 'php') as $engine) {
                $dir = dirname($rc->getFileName()).'/Resources/themes';
                if (file_exists($dir)) {
                    $container->setDefinition(
                        'liip_theme.assetic.'.$engine.'_directory_resource.'.$name,
                        AsseticExtension::createDirectoryResourceDefinition($name, $engine, array($dir))
                    );
                }
            }
        }

        // kernel themes/ directory
        foreach (array('twig', 'php') as $engine) {
            $dir = $container->getParameter('kernel.root_dir').'/Resources/themes';
            if (file_exists($dir)) {
                $container->setDefinition(
                    'liip_theme.assetic.'.$engine.'_directory_resource.kernel',
                    AsseticExtension::createDirectoryResourceDefinition('', $engine, array($dir))
                );
            }
        }
    }
}
