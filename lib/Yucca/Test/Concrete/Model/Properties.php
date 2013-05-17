<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Test\Concrete\Model;

class Properties extends Base {
    protected $yuccaProperties = array('id','first','second','third');

    protected $id;

    protected $first;
    protected $second;
    protected $third;

    /**
     * @param array $properties
     */
    public function setYuccaProperties(array $properties){
        parent::setYuccaProperties($properties);
    }

    /**
     * @return mixed
     */
    public function getId() {
        $this->hydrate('id');
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getFirst() {
        $this->hydrate('first');
        return $this->first;
    }

    /**
     * @param $first
     * @return Properties
     */
    public function setFirst($first) {
        $this->hydrate('first');
        $this->first = $first;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSecond() {
        $this->hydrate('second');
        return $this->second;
    }
    /**
     * @param $second
     * @return Properties
     */
    public function setSecond($second) {
        $this->hydrate('second');
        $this->second = $second;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getThird() {
        $this->hydrate('third');
        return $this->third;
    }
    /**
     * @param $third
     * @return Properties
     */
    public function setThird($third) {
        $this->hydrate('third');
        $this->third = $third;

        return $this;
    }
}
