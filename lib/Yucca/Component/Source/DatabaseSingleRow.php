<?php
namespace Yucca\Component\Source;

use Yucca\Component\ConnectionManager;
use Yucca\Component\SchemaManager;
use Yucca\Component\Source\Exception\NoDataException;
use Yucca\Component\Source\DataParser\DataParser;

class DatabaseSingleRow extends SourceAbstract{

    protected $tableName;

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
    }

    /**
     * @param \Yucca\Component\SchemaManager $schemaManager
     * @return \Yucca\Component\Source\DatabaseSingleRow
     */
    public function setSchemaManager(SchemaManager $schemaManager){
        $this->schemaManager = $schemaManager;
        return $this;
    }

    /**
     * @param \Yucca\Component\ConnectionManager $connectionManager
     * @return \Yucca\Component\Source\DatabaseSingleRow
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
     * Load datas for specified identifier
     * @param array $identifier
     * @param bool $rawData
     * @throws Exception\NoDataException
     * @return array
     */
    public function load(array $identifier, $rawData=false){
        $datas = $this->schemaManager->fetchOne($this->tableName, $identifier);
        if(empty($datas) || 1 != count($datas)) {
            if(count($datas)){
                throw new NoDataException("Too much datas for $this->tableName with ids : ".var_export($identifier,true));
            } else {
                throw new NoDataException("No datas for $this->tableName with ids : ".var_export($identifier,true));
            }
        }

        if($rawData) {
            return current($datas);
        } else {
            return $this->dataParser->decode(current($datas), $this->configuration['fields']);
        }

    }

    /**
     * @param array $identifier
     * @return DatabaseSingleRow
     */
    public function remove(array $identifier){
        $this->schemaManager->remove($this->tableName, $identifier);

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
        //Extract sharding key from identifier
        $originalIdentifier = $identifier;
        $shardingKey = null;
        if(isset($identifier['sharding_key'])) {
            $shardingKey = $identifier['sharding_key'];
            unset($identifier['sharding_key']);
        }

        //Check if we have to insert or update
        $insert =empty($identifier);

        //find identifiers
        $identifierFieldName = null;
        foreach($this->configuration['fields'] as $field=>$fieldProperties) {
            if(isset($fieldProperties['type']) && 'identifier' == $fieldProperties['type']) {
                $identifierFieldName = $field;
                if(array_key_exists($field, $datas) && is_null($datas[$field])) {
                    unset($datas[$field]);
                }
            }
        }

        //Check if we have datas to update or insert
        if(empty($datas)){
            throw new \Exception("Trying to save empty datas for table {$this->tableName} and identifier ".var_export($identifier,true));
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

        //Parse datas
        $datas = $this->dataParser->encode($datas, $this->configuration['fields']);

        //check if it's an insert or update
        if($insert){
            //Insert
            $affectedRows = $connection->insert($tableName, $datas);

            if(isset($identifierFieldName)) {
                return array(
                    'sharding_key' => $shardingKey,
                    $identifierFieldName=>$connection->lastInsertId()
                );
            } else {
                return $identifier;
            }
        } else {
            //Update
            $affectedRows = $connection->update($tableName, $datas, $identifier);
            return $originalIdentifier;
        }
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
