<?php
namespace Yucca\Component\ConnectionFactory;

use Doctrine\DBAL\Configuration as DBALConfiguration;
use Doctrine\DBAL\DriverManager;

class DoctrineFactory implements ConnectionFactoryInterface {
    public function getConnection(array $params) {
        return DriverManager::getConnection($params['options']);
    }
}