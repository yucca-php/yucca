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

use Doctrine\DBAL\DriverManager;

class DoctrineFactory implements ConnectionFactoryInterface {
    /**
     * @param array $params
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection(array $params) {
        return DriverManager::getConnection($params['options']);
    }
}
