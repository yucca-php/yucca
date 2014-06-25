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
class Iterator implements \Countable, \Iterator
{
    /**
     * @var \Yucca\Model\ModelInterface
     */
    protected $model = null;

    /**
     * @var \Yucca\Component\Selector\SelectorInterface
     */
    protected $selector;

    /**
     * @var \Yucca\Component\EntityManager
     */
    protected $entityManager;

    /**
     * Tell whether we return new objects or use the same one
     * If false, we keep only one model in memory to loop over, to help keeping memory low
     * @var boolean
     */
    protected $wantNewModel = false;

    /**
     * @var string
     */
    protected $modelClassName;

    /**
     * Constructor
     * @param \Yucca\Component\Selector\SelectorInterface   $selector
     * @param \Yucca\Component\EntityManager                $entityManager
     * @param string                                        $modelClassName
     */
    public function __construct(SelectorInterface $selector, EntityManager $entityManager, $modelClassName)
    {
        $this->entityManager = $entityManager;
        $this->selector = $selector;
        $this->modelClassName = $modelClassName;
    }

    /**
     * Set whether we want new object or a single one to iterate over
     * @param boolean $value
     *
     * @return \Yucca\Component\Iterator\Iterator
     */
    public function wantNewModel($value = true)
    {
        $this->wantNewModel = ($value ? true : false);

        return $this;
    }

    /**
     * initialize the unique model
     * @param mixed $id
     * @param mixed $shardingKey
     *
     * @return self
     */
    protected function initializeModel($id, $shardingKey=null)
    {
        if (is_null($this->model)) {
            $this->model = $this->entityManager->load($this->modelClassName, $id, $shardingKey);
        }

        return $this;
    }

    /**
     * give model count
     * @return int
     */
    public function count()
    {
        return $this->selector->count();
    }

    /**
     * retrieve the current Model
     * @return \Yucca\Model\ModelInterface
     */
    public function current()
    {
        if (true === $this->wantNewModel) {
            return $this->entityManager->load($this->modelClassName, $this->selector->current());
        } else {
            $this->initializeModel($this->selector->current(), $this->selector->currentShardingKey());

            $this->entityManager->resetModel($this->model, $this->selector->current());

            return $this->model;
        }
    }

    /**
     * retrieve the current key
     * @return mixed
     */
    public function key()
    {
        return $this->selector->key();
    }

    /**
     * Next
     */
    public function next()
    {
        $this->selector->next();
    }

    /**
     * First
     * @return \Yucca\Component\Iterator\Iterator
     */
    public function rewind()
    {
        $this->selector->rewind();

        return $this;
    }

    /**
     * Is there any model to fetch ?
     * @return boolean
     */
    public function valid()
    {
        return $this->selector->valid();
    }

    /**
     * @return \Yucca\Component\Selector\SelectorInterface
     */
    public function getSelector()
    {
        return $this->selector;
    }

    /**
     * @return array
     */
    public function getArray()
    {
        $toReturn = array();

        $oldWantNewModel = $this->wantNewModel;
        $this->wantNewModel(true);

        foreach ($this as $object) {
            $toReturn[] = $object;
        }
        $this->wantNewModel($oldWantNewModel);

        return $toReturn;
    }
}
