<?php

namespace Isometriks\Bundle\SpamBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('isometriks_spam');

        $rootNode
            ->children()
                ->arrayNode('timed')
                    ->canBeDisabled()
                    ->children()
                        ->scalarNode('min')->defaultValue(7)->end()
                        ->scalarNode('max')->defaultValue(3600)->end()
                        ->scalarNode('global')->defaultValue(false)->end()
                        ->scalarNode('message')
                            ->defaultValue('You are doing that too quickly')->end()
                    ->end()
                ->end()
            ->end(); 

        return $treeBuilder;
    }
}
