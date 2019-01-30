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

/**
 * Class Ids
 * @package Yucca\Component\Selector
 */
class Ids implements SelectorInterface
{
    /**
     * @var array
     */
    protected $ids;
    protected $preparedIds;
    protected $isQueryPrepared = false;

    protected $keyName;
    protected $limit = null;

    /**
     * @param array  $ids
     * @param string $keyName
     */
    public function __construct(array $ids, $keyName = null)
    {
        $this->ids = $ids;
        $this->keyName = $keyName ;
    }

    /**
     * @return mixed
     */
    public function current()
    {
        $this->prepareQuery();

        if (isset($this->keyName)) {
            return array($this->keyName => current($this->preparedIds));
        } else {
            return current($this->preparedIds);
        }
    }

    /**
     *
     */
    public function next()
    {
        $this->prepareQuery();

        next($this->preparedIds);
    }

    /**
     * @return mixed
     */
    public function key()
    {
        $this->prepareQuery();

        return key($this->preparedIds);
    }

    /**
     * @return bool
     */
    public function valid()
    {
        $this->prepareQuery();

        return ( false !== current($this->preparedIds) );
    }

    /**
     *
     */
    public function rewind()
    {
        $this->prepareQuery();

        reset($this->preparedIds);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->ids);
    }

    /**
     * @return null
     */
    public function currentShardingKey()
    {
        return null;
    }

    /**
     * @param array $criteria
     *
     * @throws \RuntimeException
     */
    public function setCriteria(array $criteria)
    {
        throw new \RuntimeException();
    }

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function limit($value)
    {
        if (0 === preg_match('/^([0-9]+)(,[0-9]+)?$/', $value)) {
            throw new \InvalidArgumentException($value.' doesn\'t match limit pattern');
        }
        $this->limit = $value;

        return $this;
    }

    /**
     *
     */
    protected function prepareQuery()
    {
        if (false === $this->isQueryPrepared) {
            if ($this->limit) {
                $limits = explode(',', $this->limit);
                if (count($limits) == 1) {
                    $this->preparedIds = array_slice($this->ids, 0, $limits[0]);
                } else {
                    $this->preparedIds = array_slice($this->ids, $limits[0], $limits[1]);
                }
            } else {
                $this->preparedIds = $this->ids;
            }
            $this->isQueryPrepared = true;
        }
    }
}
