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
 * Class NamedAppendIterator
 * @package Yucca\Component\Iterator
 */
class NamedAppendIterator extends \AppendIterator
{
    protected $names = array();

    /**
     * @param \Iterator $iterator
     * @param string    $name
     */
    public function append(\Iterator $iterator, $name = '')
    {
        parent::append($iterator);
        $this->names[] = $name;
    }

    /**
     * @return mixed
     */
    public function name()
    {
        return $this->names[$this->getIteratorIndex()];
    }
}
