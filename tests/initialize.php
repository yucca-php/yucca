<?php
/*
 * This is included first by PHPUnit to initialize autoloader
 */
require_once __DIR__.'/../../symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';
use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Doctrine\\Common' => __DIR__.'/../../doctrine-common/lib',
    'Doctrine\\DBAL'   => __DIR__.'/../../doctrine-dbal/lib',
    'Yucca\\Concrete'  => __DIR__.'/../tests',
    'Yucca'            => __DIR__.'/../lib',
));
$loader->register();

ini_set('display_errors', 1);
error_reporting(E_ALL);