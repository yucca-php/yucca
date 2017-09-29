<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Component;

use Yucca\Component\ConnectionFactory\ConnectionFactoryInterface;

/**
 * Class ConnectionManager
 * @package Yucca\Component
 */
class ConnectionManager
{
    protected $connectionsConfig;
    protected $connectionFactories = array();

    protected $connections = array();

    /**
     *
     * @param array $connectionsConfig
     */
    public function __construct(array $connectionsConfig)
    {
        $this->connectionsConfig = $connectionsConfig;
    }

    /**
     * Add a connection factory to the pool
     * @param string                                                        $connectionFactoryName
     * @param \Yucca\Component\ConnectionFactory\ConnectionFactoryInterface $connectionFactory
     * @return \Yucca\Component\ConnectionManager
     */
    public function addConnectionFactory($connectionFactoryName, ConnectionFactoryInterface $connectionFactory)
    {
        $this->connectionFactories[$connectionFactoryName] = $connectionFactory;

        return $this;
    }

    /**
     * Get connection
     * @param string $connectionName
     * @param bool   $forceFromMaster
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection($connectionName, $forceFromMaster = true)
    {
        /*if($forceFromMaster) {
            return $this->buildConnection($this->connectionsConfig[$connectionName]);
        }*/
        //FIXME : check forceFromMaster parameter
        if (false === isset($this->connections[$connectionName])) {
            if (false === isset($this->connectionsConfig[$connectionName])) {
                throw new \InvalidArgumentException("Connection \"$connectionName\" is not configured");
            }
            $this->connections[$connectionName] = $this->buildConnection($this->connectionsConfig[$connectionName]);
        }

        return $this->connections[$connectionName];
    }

    /**
     * @param string $connectionName
     *
     * @return mixed
     */
    public function getConnectionConfig($connectionName)
    {
        return $this->connectionsConfig[$connectionName];
    }

    /**
     * Build connection
     * @param array $connectionConfig
     * @throws \InvalidArgumentException
     * @return mixed
     */
    protected function buildConnection(array $connectionConfig)
    {
        if (false === isset($connectionConfig['type'])) {
            throw new \InvalidArgumentException("Connection is not well formed : missing key \"type\"");
        }

        return $this->getConnectionFactory($connectionConfig['type'])->getConnection($connectionConfig);
    }

    /**
     * Get connection factory
     * @param string $type
     * @throws \Exception
     * @return \Yucca\Component\ConnectionFactory\ConnectionFactoryInterface
     */
    protected function getConnectionFactory($type)
    {
        if (false === isset($this->connectionFactories[$type])) {
            throw new \InvalidArgumentException("Missing connection factory \"$type\"");
        }

        return $this->connectionFactories[$type];
    }
}
