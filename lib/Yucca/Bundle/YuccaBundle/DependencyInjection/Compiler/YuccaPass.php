<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Bundle\YuccaBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Class YuccaPass
 * @package Yucca\Bundle\YuccaBundle\DependencyInjection\Compiler
 */
class YuccaPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('yucca.schema_manager')) {
            //Sharding strategies
            $definition = $container->getDefinition('yucca.schema_manager');
            foreach ($container->findTaggedServiceIds('yucca.sharding_strategy') as $id => $tags) {
                foreach ($tags as $attributes) {
                    $definition->addMethodCall('addShardingStrategy', array($attributes['alias'], new Reference($id)));
                }
            }
        }

        if ($container->hasDefinition('yucca.connection_manager')) {
            //ConnectionFactories
            $definition = $container->getDefinition('yucca.connection_manager');
            foreach ($container->findTaggedServiceIds('yucca.connection_factory') as $id => $tags) {
                foreach ($tags as $attributes) {
                    $definition->addMethodCall('addConnectionFactory', array($attributes['alias'], new Reference($id)));
                }
            }
        }

        if ($container->hasDefinition('yucca.source_manager')) {
            //ConnectionFactories
            $definition = $container->getDefinition('yucca.source_manager');
            foreach ($container->findTaggedServiceIds('yucca.source_factory') as $id => $tags) {
                foreach ($tags as $attributes) {
                    $definition->addMethodCall('addSourceFactory', array($attributes['alias'], new Reference($id)));
                }
            }
        }

        if ($container->hasDefinition('yucca.selector_manager')) {
            //ConnectionFactories
            $definition = $container->getDefinition('yucca.selector_manager');
            foreach ($container->findTaggedServiceIds('yucca.selector.source_factory') as $id => $tags) {
                foreach ($tags as $attributes) {
                    $definition->addMethodCall('addSelectorSourceFactory', array($attributes['alias'], new Reference($id)));
                }
            }
        }
    }
}
