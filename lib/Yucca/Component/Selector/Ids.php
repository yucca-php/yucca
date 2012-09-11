<?php
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
