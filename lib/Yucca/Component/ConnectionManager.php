<?php
namespace Yucca\Component;

use Yucca\Component\ConnectionFactory\ConnectionFactoryInterface;

class ConnectionManager
{
    protected $connectionsConfig;
    protected $connectionFactories = array();

    protected $connections = array();

    /**
     *
     * @param array $connectionsConfig
     */
    public function __construct(array $connectionsConfig) {
        $this->connectionsConfig = $connectionsConfig;
    }

    /**
     * Add a connection factory to the pool
     * @param string $connectionFactoryName
     * @param \Yucca\Component\ConnectionFactory\ConnectionFactoryInterface $connectionFactory
     * @return \Yucca\Component\ConnectionManager
     */
    public function addConnectionFactory($connectionFactoryName, ConnectionFactoryInterface $connectionFactory) {
        $this->connectionFactories[$connectionFactoryName] = $connectionFactory;
        return $this;
    }

    /**
     * Get connection
     * @param string $connectionName
     * @param bool $forceFromMaster
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection($connectionName, $forceFromMaster=true) {
        /*if($forceFromMaster) {
            return $this->buildConnection($this->connectionsConfig[$connectionName]);
        }*/
        //FIXME : check forceFromMaster parameter
        if(false === isset($this->connections[$connectionName])) {
            if(false === isset($this->connectionsConfig[$connectionName])) {
                throw new \InvalidArgumentException("Connection \"$connectionName\" is not configured");
            }
            $this->connections[$connectionName] = $this->buildConnection($this->connectionsConfig[$connectionName]);
        }
        return $this->connections[$connectionName];
    }

    /**
     * Build connection
     * @param array $connectionConfig
     */
    protected function buildConnection(array $connectionConfig) {
        if(false === isset($connectionConfig['type'])) {
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
    protected function getConnectionFactory($type) {
        if(false === isset($this->connectionFactories[$type])) {
            throw new \InvalidArgumentException("Missing connection factory \"$type\"");
        }

        return $this->connectionFactories[$type];
    }
}
