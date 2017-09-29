<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Bundle\YuccaBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Yucca\Bundle\YuccaBundle\DependencyInjection\Compiler\YuccaPass;

/**
 * Class YuccaBundle
 * @package Yucca\Bundle\YuccaBundle
 */
class YuccaBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new YuccaPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION);
    }
}
