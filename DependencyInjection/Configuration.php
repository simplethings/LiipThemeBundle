<?php

/*
 * This file is part of the Liip/ThemeBundle
 *
 * (c) Liip AG
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Liip\ThemeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * This class contains the configuration information for the bundle
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * @author Tobias Ebnöther <ebi@liip.ch>
 * @author Roland Schilter <roland.schilter@liip.ch>
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Returns the config tree builder.
     *
     * @return \Symfony\Component\DependencyInjection\Configuration\NodeInterface
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('liip_theme', 'array');
        $rootNode
            ->children()
                ->arrayNode('themes')
                    ->useAttributeAsKey('theme')
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('active_theme')->defaultNull()->end()
                ->arrayNode('cookie')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('name')->cannotBeEmpty()->end()
                        ->scalarNode('lifetime')->defaultValue(31536000)->end()
                        ->scalarNode('path')->defaultValue('/')->end()
                        ->scalarNode('domain')->defaultValue('')->end()
                        ->booleanNode('secure')->defaultFalse()->end()
                        ->booleanNode('http_only')->defaultFalse()->end()
                    ->end()
                ->end()
                ->scalarNode('autodetect_theme')->defaultFalse()->end()
                ->booleanNode('cache_warming')->defaultTrue()->end()
            ->end()
        ;

        return $treeBuilder;
    }

}
