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

use \Yucca\Component\Source\Exception\NoDataException;
use \Yucca\Component\Source\Exception\BreakSaveChainException;
use \Yucca\Component\Source\Exception\BreakRemoveChainException;
use Yucca\Component\Source\DataParser\DataParser;

/**
 * Class Chain
 * @package Yucca\Component\Source
 */
class Chain extends SourceAbstract
{
    /**
     * @var \Yucca\Component\Source\SourceAbstract[]
     */
    protected $sources;

    /**
     * @var \Yucca\Component\Source\DataParser\DataParser
     */
    protected $dataParser;

    /**
     * Chain constructor.
     *
     * @param string $sourceName
     * @param array  $configuration
     * @param array  $sources
     */
    public function __construct($sourceName, array $configuration = array(), $sources = array())
    {
        parent::__construct($sourceName, $configuration);

        if (empty($sources)) {
            throw new \InvalidArgumentException("\"sources\" must be a non empty array");
        }
        $this->sources = $sources;
    }

    /**
     * @param string $fieldName
     *
     * @return bool
     */
    public function canHandle($fieldName)
    {
        foreach ($this->sources as $source) {
            if ($source->canHandle($fieldName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param DataParser\DataParser $dataParser
     * @return DatabaseSingleRow
     */
    public function setDataParser(DataParser $dataParser)
    {
        $this->dataParser = $dataParser;

        return $this;
    }

    /**
     * @param array $identifier
     * @param bool  $rawData
     * @param mixed $shardingKey
     *
     * @return array
     * @throws NoDataException
     * @throws \Exception
     */
    public function load(array $identifier, $rawData, $shardingKey)
    {
        $sourcesToFeed = array();
        $datas = null;
        foreach ($this->sources as $sourceKey => $source) {
            try {
                $datas = $source->load($identifier, true, $shardingKey);
                break;
            } catch (NoDataException $exception) {
                $sourcesToFeed[] = $sourceKey;
            }
        }

        if (isset($datas)) {
            foreach ($sourcesToFeed as $sourceKey) {
                $this->sources[$sourceKey]->saveAfterLoading($datas, $identifier, $shardingKey);
            }

            return $this->dataParser->decode($datas, $this->configuration['fields']);
        }

        throw new NoDataException("Chain can't load datas for source {$this->sourceName} with ids : ".var_export($identifier, true));
    }

    /**
     * @param array $identifier
     * @param null  $shardingKey
     */
    public function remove(array $identifier, $shardingKey = null)
    {
        try {
            foreach ($this->sources as $source) {
                $source->remove($identifier, $shardingKey);
            }
        } catch (BreakRemoveChainException $e) {
        }
    }

    /**
     * @param array $datas
     * @param array $identifier
     * @param null  $shardingKey
     * @param null  $affectedRows
     *
     * @throws \Exception
     */
    public function saveAfterLoading($datas, array $identifier = array(), $shardingKey = null, &$affectedRows = null)
    {
        throw new \Exception("Don't know what to do in chain {$this->sourceName}...");
    }

    /**
     * @param array $datas
     * @param array $identifier
     * @param null  $shardingKey
     * @param null  $affectedRows
     *
     * @return array
     */
    public function save($datas, array $identifier = array(), $shardingKey = null, &$affectedRows = null)
    {
        $toReturn = array();
        try {
            foreach ($this->sources as $source) {
                $justCreated = $source->save($datas, $identifier, $shardingKey, $affectedRows);
                if (is_array($justCreated)) {
                    $toReturn = array_merge($toReturn, $justCreated);
                }
            }
        } catch (BreakSaveChainException $e) {
        }

        return $toReturn;
    }
}
