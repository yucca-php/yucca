<?php

namespace Yucca\Bundle\YuccaBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Yucca\Bundle\YuccaBundle\DependencyInjection\Compiler\YuccaPass;

class YuccaBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new YuccaPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION);
    }
}
