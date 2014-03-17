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


class ShowMoreIterator extends \LimitIterator {
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
     * @param Iterator $iterator
     * @param $offset
     * @param $limit
     */
    public function __construct(Iterator $iterator, $offset, $limit) {
        $this->offset = $offset;
        $this->limit = $limit;
        parent::__construct($iterator, $offset, $limit);
    }

    /**
     * @return bool
     */
    public function canShowMore() {
        return $this->getInnerIterator()->count() > $this->limit;
    }
} 
