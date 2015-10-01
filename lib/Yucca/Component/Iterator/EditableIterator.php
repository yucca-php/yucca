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
use Yucca\Component\Selector\SelectorInterface;
use Yucca\Component\EntityManager;


/**
 * Class Iterator
 * @package Yucca\Component\Iterator
 */
class EditableIterator extends Iterator implements \ArrayAccess
{
    protected $updatedDatas = array();
    protected $deletedDatas = array();

    protected $retrieved = false;

    /**
     * @param \Yucca\Component\Selector\SelectorInterface $selector
     * @param \Yucca\Component\EntityManager $entityManager
     * @param string $modelClassName
     */
    public function __construct(SelectorInterface $selector, EntityManager $entityManager = null, $modelClassName)
    {
        parent::__construct($selector, $entityManager, $modelClassName);
        $this->wantNewModel();
    }

    /**
     * give model count
     * @return int
     */
    public function count()
    {
        if($this->retrieved) {
            return count($this->updatedDatas);
        } else {
            return parent::count();
        }
    }

    /**
     * retrieve the current Model
     * @return \Yucca\Model\ModelInterface
     */
    public function current()
    {
        if($this->retrieved) {
            return current($this->updatedDatas);
        } else {
            return parent::current();
        }
    }

    /**
     * retrieve the current key
     * @return mixed
     */
    public function key()
    {
        if($this->retrieved) {
            return key($this->updatedDatas);
        } else {
            return parent::key();
        }
    }

    /**
     * Next
     */
    public function next()
    {
        if($this->retrieved) {
            next($this->updatedDatas);

            return $this;
        } else {
            return parent::next();
        }
    }

    /**
     * First
     * @return \Yucca\Component\Iterator\Iterator
     */
    public function rewind()
    {
        if($this->retrieved) {
            reset($this->updatedDatas);

            return $this;
        } else {
            return parent::rewind();
        }
    }

    /**
     * Is there any model to fetch ?
     * @return boolean
     */
    public function valid()
    {
        if($this->retrieved) {
            return false !== current($this->updatedDatas);
        } else {
            return parent::valid();
        }
    }

    /**
     * @return \Yucca\Component\Selector\SelectorInterface
     */
    public function getSelector()
    {
        if($this->retrieved) {
            error_log('Try to '.__METHOD__.' on a retrieved iterator. The selector is no more in sync with the iterator');
        }
        return parent::getSelector();
    }

    /**
     * @return array
     */
    public function getArray()
    {
        if($this->retrieved) {
            return $this->updatedDatas;
        } else {
            return parent::getArray();
        }
    }


    /**
     * When an update come to the iterator, retrieve the original dataset, only once
     */
    protected function retrieve()
    {
        if(false === $this->retrieved) {
            $this->updatedDatas = $this->getArray();
            $this->retrieved = true;
        }
    }

    /**
     * @param mixed $offset
     * @return $this
     */
    public function offsetUnset($offset)
    {
        $this->retrieve();
        $this->deletedDatas[] = $this->updatedDatas[$offset];
        if(isset($this->updatedDatas[$offset])) {
            unset($this->updatedDatas[$offset]);
        }

        return $this;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        $this->retrieve();
        return isset($this->updatedDatas[$offset]);
    }

    /**
     * @param mixed $offset
     * @return \Yucca\Model\ModelInterface
     * @throws \Exception
     */
    public function offsetGet($offset)
    {
        $this->retrieve();

        if(isset($this->updatedDatas[$offset])) {
            return $this->updatedDatas[$offset];
        } else {
            throw new \RuntimeException('Offset does not exists : ' . $offset.'. Available offsets:'.implode(',',array_keys($this->updatedDatas)));
        }
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return $this
     */
    public function offsetSet($offset, $value)
    {
        $this->retrieve();
        $this->updatedDatas[$offset] = $value;

        return $this;
    }

    public function getUnsetData()
    {
        return $this->deletedDatas;
    }
}
