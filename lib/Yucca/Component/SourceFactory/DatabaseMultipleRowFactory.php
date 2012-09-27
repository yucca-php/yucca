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

use \Yucca\Component\Source\DatabaseMultipleRow;
use \Yucca\Component\SchemaManager;
use \Yucca\Component\ConnectionManager;
use \Yucca\Component\Source\DataParser\DataParser;

class DatabaseMultipleRowFactory implements SourceFactoryInterface
{
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
     * @param \Yucca\Component\SchemaManager $schemaManager
     * @param \Yucca\Component\ConnectionManager $connectionManager
     * @param \Yucca\Component\Source\DataParser\DataParser $dataParser
     */
    public function __construct(SchemaManager $schemaManager, ConnectionManager $connectionManager, DataParser $dataParser) {;
        $this->schemaManager = $schemaManager;
        $this->connectionManager = $connectionManager;
        $this->dataParser = $dataParser;
    }

    /**
     * build source
     * @param $sourceName
     * @param array $params
     * @return \Yucca\Component\Source\DatabaseSingleRow
     */
    public function getSource($sourceName, array $params = array()) {
        $toReturn = new DatabaseMultipleRow($sourceName, $params);
        $toReturn->setSchemaManager($this->schemaManager);
        $toReturn->setConnectionManager($this->connectionManager);
        $toReturn->setDataParser($this->dataParser);

        return $toReturn;
    }
}
