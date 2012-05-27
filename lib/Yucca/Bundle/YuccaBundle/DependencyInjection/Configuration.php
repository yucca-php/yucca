<?php

namespace Yucca\Bundle\YuccaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

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
        $rootNode = $treeBuilder->root('yucca');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        $this->addConnections($rootNode);
        $this->addSchema($rootNode);
        $this->addSources($rootNode);
        $this->addMapping($rootNode);
        $this->addSelectors($rootNode);

        return $treeBuilder;
    }

    protected function addSchema(ArrayNodeDefinition $rootNode) {
        $rootNode
            ->children()
                ->arrayNode('schema')
                    ->useAttributeAsKey('alias')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('sharding_strategy')->end()
                            ->scalarNode('sharding_strategy_options')->end()
                            ->arrayNode('shards')
                                ->prototype('scalar')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    protected function addConnections(ArrayNodeDefinition $rootNode) {
        $rootNode
            ->children()
                ->arrayNode('connections')
                    ->useAttributeAsKey('alias')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('type')
                            ->end()
                            ->arrayNode('options')
                                ->useAttributeAsKey('alias')
                                ->prototype('variable')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    protected function addSources(ArrayNodeDefinition $rootNode) {
        $rootNode
            ->children()
                ->arrayNode('sources')
                    ->useAttributeAsKey('alias')
                    ->prototype('variable')
                    ->end()
                ->end()
            ->end()
        ;
    }

    protected function addSelectors(ArrayNodeDefinition $rootNode) {
        $rootNode
            ->children()
                ->arrayNode('selectors')
                    ->useAttributeAsKey('alias')
                    ->prototype('variable')
                    ->end()
                ->end()
            ->end()
        ;
    }

    protected function addMapping(ArrayNodeDefinition $rootNode) {
        $rootNode
            ->children()
                ->arrayNode('mapping')
                    ->useAttributeAsKey('alias')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('mapper_class_name')
                            ->end()
                            ->arrayNode('properties')
                                ->useAttributeAsKey('alias')
                                ->prototype('variable')
                                ->end()
                            ->end()
                            ->arrayNode('sources')
                                ->useAttributeAsKey('alias')
                                ->prototype('variable')
                                ->end()
                            ->end()
                            ->arrayNode('selectors')
                                ->useAttributeAsKey('alias')
                                ->prototype('variable')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
