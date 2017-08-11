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

class PhpArray extends SourceAbstract
{
    static protected $datas;

    /**
     * @var \Yucca\Component\Source\DataParser\DataParser
     */
    protected $dataParser;

    /**
     * Constructor
     * @param $sourceName
     * @param array $configuration
     * @param string $prefix
     * @throws \InvalidArgumentException
     */
    public function __construct($sourceName, array $configuration=array(), $prefix='') {
        parent::__construct($sourceName, $configuration);
    }

    /**
     * @param $identifier
     * @return string
     */
    protected function getCacheKey($identifier){
        $toReturn = $this->sourceName.'_'.(isset($this->configuration['version']) ? $this->configuration['version'] : 1);
        foreach($identifier as $k=>$v) {
            if($v instanceof \Yucca\Model\ModelInterface) {
                $v = $v->getId();
            }
            $toReturn .= ':'.$k.'='.$v;
        }
        return $toReturn;
    }

    /**
     * @param DataParser $dataParser
     * @return DatabaseSingleRow
     */
    public function setDataParser(DataParser $dataParser){
        $this->dataParser = $dataParser;
        return $this;
    }

    /**
     * @param array $identifier
     * @param bool $rawData
     * @return array
     * @throws Exception\NoDataException
     */
    public function load(array $identifier, $rawData, $shardingKey) {
        $cacheKey = $this->getCacheKey($identifier);
        if(isset(static::$datas[$cacheKey])) {
            $datas = static::$datas[$cacheKey];

            if($rawData) {
                return $datas;
            } else {
                return $this->dataParser->decode($datas, $this->configuration['fields']);
            }
        }

        throw new NoDataException("No datas found in cache for \"{$this->sourceName}\" with identifiers ".var_export($identifier, true));
    }

    /**
     * @param array $identifier
     * @return Memcache
     */
    public function remove(array $identifier, $shardingKey=null)
    {
        unset(static::$datas[$this->getCacheKey($identifier)]);

        return $this;
    }

    /**
     * @param $serializedCriterias
     * @param array $options
     * @return array|string
     * @throws Exception\NoDataException
     */
    public function loadIds($serializedCriterias, array $options=array())
    {
        if(isset(static::$datas[$serializedCriterias])) {
            return static::$datas[$serializedCriterias];
        }

        throw new NoDataException("No datas found in cache for \"{$this->sourceName}\" with criterias ".var_export($serializedCriterias, true));
    }

    /**
     * @param $datas
     * @param array $identifier
     * @param null $shardingKey
     * @param null $affectedRows
     */
    public function save($datas, array $identifier=array(), $shardingKey=null, &$affectedRows=null)
    {
        unset(static::$datas[$this->getCacheKey($identifier)]);
    }

    /**
     * @param $datas
     * @param array $identifier
     * @param null $shardingKey
     * @param null $affectedRows
     */
    public function saveAfterLoading($datas, array $identifier=array(), $shardingKey=null, &$affectedRows=null)
    {
        static::$datas[$this->getCacheKey($identifier)] = $datas;
    }
}
