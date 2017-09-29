<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Component\Selector\SourceFactory;

use \Yucca\Component\Selector\Source\Database;
use \Yucca\Component\SchemaManager;

/**
 * Class DatabaseFactory
 * @package Yucca\Component\Selector\SourceFactory
 */
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
    public function __construct(SchemaManager $schemaManager)
    {
        $this->schemaManager = $schemaManager;
    }

    /**
     * build source
     * @param array $params
     * @return \Yucca\Component\Source\DatabaseSingleRow
     */
    public function getSource(array $params = array())
    {
        $toReturn = new Database();
        $toReturn->setSchemaManager($this->schemaManager);

        return $toReturn;
    }
}
