<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Component\SourceFactory;

use \Yucca\Component\Source\Memcache;
use \Yucca\Component\ConnectionManager;
use \Yucca\Component\Source\DataParser\DataParser;

class MemcacheFactory implements SourceFactoryInterface
{
    /**
     * @var \Yucca\Component\ConnectionManager
     */
    protected $connectionManager;

    /**
     * @var \Yucca\Component\Source\DataParser\DataParser
     */
    protected $dataParser;

    /**
     * @param \Yucca\Component\ConnectionManager $connectionManager
     * @param \Yucca\Component\Source\DataParser\DataParser $dataParser
     */
    public function __construct(ConnectionManager $connectionManager, DataParser $dataParser) {
        $this->connectionManager = $connectionManager;
        $this->dataParser = $dataParser;
    }

    /**
     * Build source
     * @param $sourceName
     * @param array $params
     * @return \Yucca\Component\Source\Memcache
     */
    public function getSource($sourceName, array $params = array()) {
        if(false===isset($params['connection_name'])){
            throw new \InvalidArgumentException("Configuration array must contain a 'connection_name' key");
        }
        $connectionConfig = $this->connectionManager->getConnectionConfig($params['connection_name']);
        $toReturn = new Memcache($sourceName, $params, isset($connectionConfig['options']['prefix']) ? $connectionConfig['options']['prefix'] : '');
        $toReturn->setConnectionManager($this->connectionManager);
        $toReturn->setDataParser($this->dataParser);

        return $toReturn;
    }
}
