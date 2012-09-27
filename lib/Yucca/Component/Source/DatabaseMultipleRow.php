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

use Yucca\Component\ConnectionManager;
use Yucca\Component\SchemaManager;
use Yucca\Component\Source\Exception\NoDataException;
use Yucca\Component\Source\DataParser\DataParser;

/**
 * @todo : handle data parser
 */
class DatabaseMultipleRow extends SourceAbstract{

    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var string
     */
    protected $nameField;

    /**
     * @var string
     */
    protected $valueField;

    /**
     * @var string
     */
    protected $mapping;

    /**
     * @var \Yucca\Component\SchemaManager
     */
    protected $schemaManager;

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

        if(false===isset($this->configuration['table_name'])){
            throw new \InvalidArgumentException("Configuration array must contain a 'table_name' key");
        }
        $this->tableName = $this->configuration['table_name'];

        if(false===isset($this->configuration['name_field'])){
            throw new \InvalidArgumentException("Configuration array must contain a 'name_field' key");
        }
        $this->nameField = $this->configuration['name_field'];

        if(false===isset($this->configuration['value_field'])){
            throw new \InvalidArgumentException("Configuration array must contain a 'value_field' key");
        }
        $this->valueField = $this->configuration['value_field'];

        if(false===isset($this->configuration['mapping'])){
            throw new \InvalidArgumentException("Configuration array must contain a 'mapping' key");
        }
        $this->mapping = $this->configuration['mapping'];
    }

    /**
     * @param \Yucca\Component\SchemaManager $schemaManager
     * @return \Yucca\Component\Source\DatabaseMultipleRow
     */
    public function setSchemaManager(SchemaManager $schemaManager){
        $this->schemaManager = $schemaManager;
        return $this;
    }

    /**
     * @param \Yucca\Component\ConnectionManager $connectionManager
     * @return \Yucca\Component\Source\DatabaseMultipleRow
     */
    public function setConnectionManager(ConnectionManager $connectionManager){
        $this->connectionManager = $connectionManager;
        return $this;
    }

    /**
     * @param \Yucca\Component\Source\DataParser\DataParser $dataParser
     * @return \Yucca\Component\Source\DatabaseMultipleRow
     */
    public function setDataParser(DataParser $dataParser){
        $this->dataParser = $dataParser;
        return $this;
    }

    /**
     * @param array $identifier
     * @return array
     * @throws \RuntimeException
     */
    protected function mapIdentifier(array $identifier) {
        $mappedIdentifier = array();
        foreach($identifier as $key=>$value) {
            if(false === isset($this->mapping[$key])) {
                throw new \RuntimeException('Missing field mapping key : '.$key);
            }
            $mappedIdentifier[$this->mapping[$key]] = $value;
        }

        return $mappedIdentifier;
    }

    /**
     * Load datas for specified identifier
     * @param array $identifier
     * @throws Exception\NoDataException
     * @return array
     */
    public function load(array $identifier){
        $mappedIdentifier = $this->mapIdentifier($identifier);
        $datas = $this->schemaManager->fetchIds($this->tableName, $mappedIdentifier, array($this->nameField, $this->valueField));
        $toReturn = array();
        foreach($datas as $row) {
            $toReturn[$row[$this->nameField]] = $row[$this->valueField];
        }

        return $toReturn;
    }

    /**
     * @param array $identifier
     * @return \Yucca\Component\Source\DatabaseMultipleRow
     */
    public function remove(array $identifier){
        $this->schemaManager->remove($this->tableName, $this->mapIdentifier($identifier));

        return $this;
    }

    /**
     * Save datas
     * @param $datas
     * @param array $identifier
     * @param array $affectedRows
     * @throws \Exception
     * @return int
     */
    public function save($datas, array $identifier=array(), &$affectedRows=null){
        $datasWithoutIdentifiers=array();
        foreach($datas as $key=>$value){
            if(isset($this->mapping[$key])) {
                $identifier[$key] = $value;
            } else {
                $datasWithoutIdentifiers[$key]=$value;
            }
        }
        $datas = $datasWithoutIdentifiers;
        //Get new identifiers
        $mappedIdentifier = $this->mapIdentifier($identifier);

        //Extract sharding key from identifier
        $shardingKey = null;
        if(isset($mappedIdentifier['sharding_key'])) {
            $shardingKey = $mappedIdentifier['sharding_key'];
            unset($mappedIdentifier['sharding_key']);
        }

        //Get Connection and table name
        $connection = $this->connectionManager->getConnection(
            $this->schemaManager->getConnectionName($this->tableName, $shardingKey, true),
            true
        );
        $shardingIdentifier = $this->schemaManager->getShardingIdentifier($this->tableName,$shardingKey);
        $tableName = $this->tableName;
        if($shardingIdentifier) {
            $tableName = sprintf('%1$s_%2$s', $this->tableName, $shardingIdentifier);
        }

        //First, remove all
        if(false === empty($identifier)) {
            $this->remove($identifier);
        }

        //then Insert
        $affectedRows = 0;
        foreach($datas as $key=>$value) {
            if(false === array_key_exists($key, $identifier)) {
                $connection->insert(
                    $tableName,
                    array_merge(
                        $mappedIdentifier,
                        array(
                            $this->nameField=>$key,
                            $this->valueField=>$value,
                        )
                    )
                );
                $affectedRows++;
            }
        }

        return $identifier;
    }

    /**
     * Save datas
     * @param $datas
     * @param array $identifier
     * @param array $affectedRows
     * @return int
     */
    public function saveAfterLoading($datas, array $identifier=array(), &$affectedRows=null){
        return $this->save($datas, $identifier, $affectedRows);
    }
}
