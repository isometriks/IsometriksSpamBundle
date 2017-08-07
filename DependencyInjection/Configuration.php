<?php

namespace Isometriks\Bundle\SpamBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
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
                        ->booleanNode('global')->defaultFalse()->end()
                        ->scalarNode('message')
                            ->defaultValue('You are doing that too quickly')->end()
                    ->end()
                ->end()

                ->arrayNode('honeypot')
                    ->canBeDisabled()
                    ->children()
                        ->scalarNode('field')->defaultValue('email_address')->end()
                        ->booleanNode('use_class')->defaultFalse()->end()
                        ->scalarNode('hide_class')->defaultValue('hidden')->end()
                        ->booleanNode('global')->defaultFalse()->end()
                        ->scalarNode('message')
                            ->defaultValue('Form fields are invalid')->end()
                    ->end()
                ->end()
            
            
                ->arrayNode('cookie')
                    ->canBeDisabled()
                    ->children()
                        ->scalarNode('name')->defaultValue('antispam')->end()
                        ->booleanNode('global')->defaultFalse()->end()
                        ->scalarNode('message')
                            ->defaultValue('Something is wrong, please try again')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
