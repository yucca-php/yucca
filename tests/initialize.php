<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// This is included first by PHPUnit to initialize autoloader
require_once __DIR__.'/../../symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';
use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Doctrine\\Common' => __DIR__.'/../../doctrine-common/lib',
    'Doctrine\\DBAL'   => __DIR__.'/../../doctrine-dbal/lib',
    'Yucca\\Concrete'  => __DIR__,
    'Yucca'            => __DIR__.'/../lib',
));
$loader->register();

ini_set('display_errors', 1);
error_reporting(E_ALL);
