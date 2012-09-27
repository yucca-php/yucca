<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Component\Source;

use Yucca\Component\Source\Exception\NoDataException;
use Yucca\Component\ConnectionManager;
use Yucca\Component\Source\DataParser\DataParser;

class Memcache extends SourceAbstract
{
    protected $connectionName;

    /**
     * @var \Yucca\Component\ConnectionManager
     */
    protected $connectionManager;

    /**
     * @var \Yucca\Component\Source\DataParser\DataParser
     */
    protected $dataParser;

    /**
     * Constructor
     * @param $sourceName
     * @param array $configuration
     * @throws \InvalidArgumentException
     */
    public function __construct($sourceName, array $configuration=array()) {
        parent::__construct($sourceName, $configuration);

        if(false===isset($this->configuration['connection_name'])){
            throw new \InvalidArgumentException("Configuration array must contain a 'connection_name' key");
        }
        $this->connectionName = $this->configuration['connection_name'];
    }

    /**
     * @param \Yucca\Component\ConnectionManager $connectionManager
     * @return \Yucca\Component\Source\Memcache
     */
    public function setConnectionManager(ConnectionManager $connectionManager){
        $this->connectionManager = $connectionManager;
        return $this;
    }

    /**
     * @param DataParser\DataParser $dataParser
     * @return DatabaseSingleRow
     */
    public function setDataParser(DataParser $dataParser){
        $this->dataParser = $dataParser;
        return $this;
    }

    /**
     * @param $identifier
     * @return string
     */
    protected function getCacheKey($identifier){
        $toReturn = $this->sourceName;
        foreach($identifier as $k=>$v) {
            $toReturn .= ':'.$k.'='.$v;
        }
        return $toReturn;
    }

    /**
     * @throws \Exception
     * @return \Memcache
     */
    protected function getConnection(){
        $toReturn = $this->connectionManager->getConnection($this->connectionName);
        if($toReturn instanceof \Memcache) {
            return $toReturn;
        }

        throw new \Exception("Connection binded to \"{$this->sourceName}\" must be a \"memcache\" connection");
    }

    /**
     * @param array $identifier
     * @param bool $rawData
     * @return array
     * @throws Exception\NoDataException
     */
    public function load(array $identifier, $rawData=false) {
        $datas = $this->getConnection()->get($this->getCacheKey($identifier));

        if(false === $datas) {
            throw new NoDataException("No datas found in cache for \"{$this->sourceName}\" with identifiers ".var_export($identifier, true));
        }


        if($rawData) {
            return $datas;
        } else {
            return $this->dataParser->decode($datas, $this->configuration['fields']);
        }
    }

    /**
     * @param array $identifier
     * @return Memcache
     */
    public function remove(array $identifier) {
        $this->getConnection()->delete($this->getCacheKey($identifier));

        return $this;
    }

    /**
     * @param $serializedCriterias
     * @param array $options
     * @throws Exception\NoDataException
     */
    public function loadIds($serializedCriterias, array $options=array()) {
        $datas = $this->getConnection()->get($serializedCriterias);

        if(false === $datas) {
            throw new NoDataException("No datas found in cache for \"{$this->sourceName}\" with identifiers ".var_export($identifier, true));
        }

        return $datas;
    }

    public function save($datas, array $identifier=array(), &$affectedRows=null) {
        $this->getConnection()->delete($this->getCacheKey($identifier));
    }

    public function saveAfterLoading($datas, array $identifier=array(), &$affectedRows=null) {
        $this->getConnection()->set($this->getCacheKey($identifier), $datas, 0, 0);
    }
}
