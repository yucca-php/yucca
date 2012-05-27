<?php

namespace Yucca\Bundle\YuccaBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Adds tagged twig.extension services to twig service
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class YuccaPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('yucca.schema_manager')) {
            //Sharding strategies
            $definition = $container->getDefinition('yucca.schema_manager');
            foreach ($container->findTaggedServiceIds('yucca.sharding_strategy') as $id => $tags) {
                foreach($tags as $attributes) {
                    $definition->addMethodCall('addShardingStrategy', array($attributes['alias'], new Reference($id)));
                }
            }
        }

        if ($container->hasDefinition('yucca.connection_manager')) {
            //ConnectionFactories
            $definition = $container->getDefinition('yucca.connection_manager');
            foreach ($container->findTaggedServiceIds('yucca.connection_factory') as $id => $tags) {
                foreach($tags as $attributes) {
                    $definition->addMethodCall('addConnectionFactory', array($attributes['alias'], new Reference($id)));
                }
            }
        }

        if ($container->hasDefinition('yucca.source_manager')) {
            //ConnectionFactories
            $definition = $container->getDefinition('yucca.source_manager');
            foreach ($container->findTaggedServiceIds('yucca.source_factory') as $id => $tags) {
                foreach($tags as $attributes) {
                    $definition->addMethodCall('addSourceFactory', array($attributes['alias'], new Reference($id)));
                }
            }
        }

        if ($container->hasDefinition('yucca.selector_manager')) {
            //ConnectionFactories
            $definition = $container->getDefinition('yucca.selector_manager');
            foreach ($container->findTaggedServiceIds('yucca.selector.source_factory') as $id => $tags) {
                foreach($tags as $attributes) {
                    $definition->addMethodCall('addSelectorSourceFactory', array($attributes['alias'], new Reference($id)));
                }
            }
        }
    }
}
