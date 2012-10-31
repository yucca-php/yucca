<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Component;

use Yucca\Model\ModelInterface;

class EntityManager
{
    protected $defaultIdKey;

    /**
     * @var \Yucca\Component\MappingManager
     */
    protected $mappingManager;

    /**
     * @var \Yucca\Component\SelectorManager
     */
    protected $selectorManager;

    /**
     * @param string $defaultIdKey
     */
    public function __construct($defaultIdKey = 'id') {
        $this->defaultIdKey = $defaultIdKey;
    }

    /**
     * @param \Yucca\Component\MappingManager $mappingManager
     */
    public function setMappingManager(MappingManager $mappingManager) {
        $this->mappingManager = $mappingManager;
    }

    /**
     * @param \Yucca\Component\SelectorManager $selectorManager
     */
    public function setSelectorManager(SelectorManager $selectorManager) {
        $this->selectorManager = $selectorManager;
    }

    /**
     * @return \Yucca\Component\SelectorManager
     */
    public function getSelectorManager() {
        return $this->selectorManager;
    }

    /**
     * @param \Yucca\Model\ModelInterface $model
     * @return EntityManager
     */
    protected function initModel(ModelInterface $model){
        $model->setYuccaMappingManager($this->mappingManager)
                ->setYuccaSelectorManager($this->selectorManager)
                ->setYuccaEntityManager($this);

        return $this;
    }

    /**
     * @param $entityClassName
     * @param $identifier
     * @param mixed $shardingKey
     * @throws \Exception
     * @return \Yucca\Model\ModelInterface
     */
    public function load($entityClassName, $identifier, $shardingKey=null){
        //If $identifier isn't an array, assumed it's the id value
        if(false === is_array($identifier)) {
            $identifier = array($this->defaultIdKey=>$identifier);
        }

        //if we have a sharding key, add it to the identifier array
        if(false === is_null($shardingKey)) {
            $identifier['sharding_key'] = $shardingKey;
        }

        //create object
        if(false === class_exists($entityClassName)){
            throw new \Exception("Entity class $entityClassName not found.");
        }

        /**
         * @var \Yucca\Model\ModelInterface $toReturn
         */
        $toReturn = new $entityClassName();

        //@Fixme : generate proxy like doctrine
        if(false === ($toReturn instanceof ModelInterface)) {
            throw new \Exception("Entity class $entityClassName must implement \Yucca\Model\ModelInterface.");
        }

        $this->initModel($toReturn);
        $toReturn->setYuccaIdentifier($identifier);

        return $toReturn;
    }

    public function save(ModelInterface $model){
        $this->initModel($model);

        $model->save();

        return $this;
    }

    public function remove(ModelInterface $model){
        $this->initModel($model);

        $model->remove();

        return $this;
    }

    public function resetModel(ModelInterface $model, $identifier) {
        $model->reset($identifier);

        return $this;
    }
}
