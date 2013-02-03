<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yucca\Component\Selector;

use \Yucca\Component\Selector\Exception\NoDataException;
use \Yucca\Component\Selector\Source\SelectorSourceInterface;
use Yucca\Component\Selector\Exception\PointerException;

/**
 * Selector Abstract
 * @author rjanot
 */
abstract class SelectorAbstract implements SelectorInterface
{
    protected $options = array();
    protected $criterias = array();
    protected $isQueryPrepared = false;
    protected $idsArray = array();
    protected $idsCount = null;
    protected $orderBy = null;
    protected $groupBy = null;
    protected $offset = null;
    protected $limit = null;

    /**
     * @var SelectorSourceInterface
     */
    protected $source;

    protected $idFields;
    protected $shardingKeyField;
    protected $table;

    protected $SELECTOR_NAME;
    private $VERSION = '1';
    protected $SPECIFIC_VERSION_OFFSET = '1';

    /**
     * @param Source\SelectorSourceInterface $source
     */
    public function __construct(SelectorSourceInterface $source = null)
    {
        $this->SELECTOR_NAME = get_class($this);

        $this->setSource($source);
    }

    /**
     * Set the source used by the selector
     * @param SelectorSourceInterface $source
     * @return \Yucca\Component\Selector\SelectorAbstract
     */
    public function setSource(SelectorSourceInterface $source = null)
    {
        $this->source = $source;
        return $this;
    }

    public function invalidateGlobal(){
        $this->source->invalidateGlobal($this->options);
    }

    /**
     * prepare ids : there the criterias are fetched
     * @throws Exception\NoDataException
     * @return \Yucca\Component\Selector\SelectorAbstract
     */
    protected function prepareQuery() {
        if(false === $this->isQueryPrepared) {
            $options = array(
                SelectorSourceInterface::RESULT => SelectorSourceInterface::RESULT_IDENTIFIERS,
                SelectorSourceInterface::LIMIT => $this->limit,
                SelectorSourceInterface::OFFSET => $this->offset,
                SelectorSourceInterface::ORDERBY => $this->orderBy,
                SelectorSourceInterface::GROUPBY => $this->groupBy,
            );
            $this->idsArray = $this->source->loadIds(
                $this->criterias,
                array_merge($this->options, $options)
            );
            $this->isQueryPrepared = true;
        }

        return $this;
    }

    /**
     * prepare count : there the criterias are fetched
     * @throws Exception\NoDataException
     * @return \Yucca\Component\Selector\SelectorAbstract
     */
    protected function prepareCount() {
        if(is_null($this->idsCount)) {
            $options = array(
                SelectorSourceInterface::RESULT => SelectorSourceInterface::RESULT_COUNT,
            );
            $this->idsCount = $this->source->loadIds(
                $this->criterias,
                array_merge($this->options, $options)
            );
        }

        return $this;
    }

    /**
     * Get content
     * @return array
     */
    public function getIds( )
    {
        $this->prepareQuery(false);
        return $this->idsArray;
    }

    /**
     * Reset criterias
     * @return \Yucca\Component\Selector\SelectorAbstract
     */
    public function reset()
    {
        $this->criterias = array();
        return $this;
    }

    /**
     * Implements Countable interface - return
     * @return int
     */
    public function count()
    {
        $this->prepareCount(true);
        return $this->idsCount;
    }

    /**
     * Implements Iterator interface
     * @throws PointerException
     * @return int
     */
    public function current()
    {
        $this->prepareQuery();

        if( false !== current($this->idsArray) )
        {
            return current($this->idsArray);
        }
        else
        {
            throw new PointerException('Can\'t retrieve the current element');
        }
    }

    /**
     * Not applicable here, but for abstract purpose
     * @return null
     */
    public function currentShardingKey()
    {
        return null;
    }

    /**
     * Implements Iterator interface
     * @return mixed
     */
    public function key()
    {
        $this->prepareQuery();
        return key($this->idsArray);
    }

    /**
     * Implements Iterator interface
     */
    public function next()
    {
        $this->prepareQuery();
        next($this->idsArray);
    }

    /**
     * Implements Iterator interface
     */
    public function rewind()
    {
        $this->prepareQuery();
        reset($this->idsArray);
    }

    /**
     * Implements Iterator interface
     * @return boolean
     */
    public function valid()
    {
        $this->prepareQuery();
        return ( false !== current($this->idsArray) );
    }

    public function orderBy($value)
    {
        $this->orderBy = $value;
        return $this;
    }

    public function offset($value)
    {
        $this->offset = $value;
        return $this;
    }

    public function limit($value)
    {
        if(0===preg_match('/^([0-9]+)(,[0-9]+)?$/',$value)){
            throw new \InvalidArgumentException($value.' doesn\'t match limit pattern');
        }
        $this->limit = $value;
        return $this;
    }
}
