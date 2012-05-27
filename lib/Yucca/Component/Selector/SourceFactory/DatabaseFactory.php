<?php
namespace Yucca\Component\Selector\SourceFactory;

use \Yucca\Component\Selector\Source\Database;
use \Yucca\Component\SchemaManager;
use \Yucca\Component\ConnectionManager;

class DatabaseFactory implements SelectorSourceFactoryInterface
{
    /**
     * @var \Yucca\Component\SchemaManager
     */
    protected $schemaManager;

    /**
     * construct
     * @param \Yucca\Component\SchemaManager $schemaManager
     */
    public function __construct(SchemaManager $schemaManager) {
        $this->schemaManager = $schemaManager;
    }

    /**
     * build source
     * @param $sourceName
     * @param array $params
     * @return \Yucca\Component\Source\DatabaseSingleRow
     */
    public function getSource(array $params = array()) {
        $toReturn = new Database();
        $toReturn->setSchemaManager($this->schemaManager);

        return $toReturn;
    }
}
