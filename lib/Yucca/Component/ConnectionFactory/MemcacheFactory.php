<?php
namespace Yucca\Component\ConnectionFactory;


class MemcacheFactory implements ConnectionFactoryInterface {
    public function getConnection(array $params) {
        $addServerParamsDefault = array(
            'host'=>null,
            'post'=>null,
        );

        $memcache = new \Memcache();
        foreach($params['options']['servers'] as $server){
            $addServerParams = array_merge($addServerParamsDefault, $server);
            $memcache->addserver($addServerParams['host'], $addServerParams['port']);
        }
        return $memcache;
    }
}