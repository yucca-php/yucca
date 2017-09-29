<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Component\Iterator;

/**
 * Class ShowMoreIterator
 * @package Yucca\Component\Iterator
 */
class ShowMoreIterator extends \LimitIterator implements \Countable
{
    protected $offset;
    protected $limit;

    /**
     * @return mixed
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return mixed
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @return mixed
     */
    public function getNextOffset()
    {
        return $this->offset + $this->limit;
    }

    /**
     * ShowMoreIterator constructor.
     *
     * @param Iterator $iterator
     * @param mixed    $offset
     * @param mixed    $limit
     */
    public function __construct(Iterator $iterator, $offset, $limit)
    {
        $this->offset = $offset;
        $this->limit = $limit;
        parent::__construct($iterator, 0, $limit);
    }

    /**
     * @return bool
     */
    public function canShowMore()
    {
        return $this->getInnerIterator()->count() > ($this->offset + $this->limit);
    }

    /**
     * give model count
     * @return int
     */
    public function count()
    {
        return $this->getInnerIterator()->count();
    }
}
