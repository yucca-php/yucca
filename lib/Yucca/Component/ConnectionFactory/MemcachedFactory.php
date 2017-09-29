<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Component\ConnectionFactory;

/**
 * Class MemcachedFactory
 * @package Yucca\Component\ConnectionFactory
 */
class MemcachedFactory implements ConnectionFactoryInterface
{
    /**
     * @param array $params
     * @return \Memcache
     */
    public function getConnection(array $params)
    {
        $addServerParamsDefault = array(
            'host'=>null,
            'port'=>null,
        );

        $memcache = new \Memcached();
        foreach ($params['options']['servers'] as $server) {
            $addServerParams = array_merge($addServerParamsDefault, $server);
            $memcache->addServer($addServerParams['host'], $addServerParams['port']);
        }

        return $memcache;
    }
}
