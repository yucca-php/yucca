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

class Ids implements SelectorInterface {
    /**
     * @var array
     */
    protected $ids;

    protected $keyName;

    /**
     * @param array $ids
     * @param string $keyName
     */
    public function __construct(array $ids, $keyName = null) {
        $this->ids = $ids;
        $this->keyName = $keyName ;
    }

    /**
     * @return mixed
     */
    public function current()
    {
        if(isset($this->keyName)) {
            return array($this->keyName => current($this->ids));
        } else {
            return current($this->ids);
        }
    }

    /**
     *
     */
    public function next()
    {
        next($this->ids);
    }

    /**
     *
     */
    public function key()
    {
        return key($this->ids);
    }

    /**
     *
     */
    public function valid()
    {
        return ( false !== current($this->ids) );
    }

    /**
     *
     */
    public function rewind()
    {
        reset($this->ids);
    }

    /**
     *
     */
    public function count()
    {
        count($this->ids);
    }

    /**
     * @return null
     */
    public function currentShardingKey()
    {
        return null;
    }
}
