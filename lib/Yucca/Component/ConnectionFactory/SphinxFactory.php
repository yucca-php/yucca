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


class SphinxFactory implements ConnectionFactoryInterface {
    /**
     * @param array $params
     * @return \Memcache
     */
    public function getConnection(array $params) {
        $addServerParamsDefault = array(
            'host'=>null,
            'port'=>null,
            'timeout'=>1,
        );

        $sphinx = new \SphinxClient();

        $addServerParams = array_merge($addServerParamsDefault, $params['options']);
        $sphinx->setServer($addServerParams['host'], $addServerParams['port']);
        $sphinx->SetConnectTimeout($addServerParams['timeout']);

        return $sphinx;
    }
}
