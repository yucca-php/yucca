<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Model;

use Yucca\Component\EntityManager;
use Yucca\Component\MappingManager;
use Yucca\Component\SelectorManager;

abstract class ModelAbstract {
    protected $yuccaInitialized = array();
    protected $yuccaProperties = array();

    /**
     * @var \Yucca\Component\MappingManager
     */
    protected $yuccaMappingManager;
    /**
     * @var \Yucca\Component\SelectorManager
     */
    protected $yuccaSelectorManager;
    /**
     * @var \Yucca\Component\EntityManager
     */
    protected $yuccaEntityManager;

    protected $yuccaIdentifier;

    abstract public function getId();

    /**EntityManager
     * @param \Yucca\Component\MappingManager $mappingManager
     * @return ModelAbstract
     */
    public function setYuccaMappingManager(MappingManager $mappingManager) {
        $this->yuccaMappingManager = $mappingManager;

        return $this;
    }

    /**
     * @param \Yucca\Component\EntityManager $entityManager
     * @return ModelAbstract
     */
    public function setYuccaEntityManager(EntityManager $entityManager) {
        $this->yuccaEntityManager = $entityManager;

        return $this;
    }

    /**
     * @param \Yucca\Component\SelectorManager $selectorManager
     * @return ModelAbstract
     */
    public function setYuccaSelectorManager(SelectorManager $selectorManager) {
        $this->yuccaSelectorManager = $selectorManager;

        return $this;
    }

    /**
     * @param $identifier
     * @return ModelAbstract
     */
    public function setYuccaIdentifier($identifier) {
        $this->yuccaIdentifier = $identifier;

        return $this;
    }

    /**
     * @param $identifier
     * @return ModelAbstract
     */
    public function reset($identifier) {
        $this->yuccaIdentifier = $identifier;
        foreach($this->yuccaProperties as $propertyName) {
            $this->$propertyName = null;
        }
        $this->yuccaInitialized = array();

        return $this;
    }

    /**
     * @param $properties
     * @return ModelAbstract
     */
    protected function setYuccaProperties(array $properties) {
        $this->yuccaProperties = $properties;

        return $this;
    }

    /**
     * Hydrate this model with information coming from the mapping manager
     * @param $propertyName
     * @return ModelAbstract
     */
    protected function hydrate($propertyName) {
        if(isset($this->yuccaMappingManager) && (false === isset($this->yuccaInitialized[$propertyName])) && false===empty($this->yuccaIdentifier)){
            $values = $this->yuccaMappingManager->getMapper(get_class($this))->load($this->yuccaIdentifier, $propertyName);
            foreach($values as $property=>$value) {
                $this->$property = $value;
                $this->yuccaInitialized[$property] = true;
            }
        }
        $this->yuccaInitialized[$propertyName] = true;

        return $this;
    }

    /**
     * @throws \Yucca\Component\Source\Exception\NoDataException
     */
    public function ensureExist(){
        if(empty($this->yuccaIdentifier)){
            throw new \Yucca\Component\Source\Exception\NoDataException(get_class($this).' doesn\'t seems to be saved in database');
        }
        try {
            foreach($this->yuccaProperties as $propertyName) {
                if(false === isset($this->yuccaInitialized[$propertyName])) {
                    $this->hydrate($propertyName);
                }
            }
        } catch(\Yucca\Component\Source\Exception\NoDataException $exception){
            throw new \Yucca\Component\Source\Exception\NoDataException(get_class($this).' doesn\'t seems to exist with identifiers : '.var_export($this->yuccaIdentifier,true));
        }
    }

    /**
     * @return ModelAbstract
     * @throws \Exception
     */
    public function save(){
        // Check that we have a mapping
        if(false === isset($this->yuccaMappingManager)){
            throw new \Exception("Mapping manager isn't set");
        }

        //load values
        foreach($this->yuccaProperties as $propertyName) {
            if(false === isset($this->yuccaInitialized[$propertyName])) {
                $this->hydrate($propertyName);
            }
        }

        //Create value list to save
        $toSave = array();
        foreach($this->yuccaProperties as $propertyName) {
            $toSave[$propertyName] = $this->$propertyName;
        }

        //Save
        $this->yuccaIdentifier = $this->yuccaMappingManager->getMapper(get_class($this))->save($this->yuccaIdentifier, $toSave);

        //Set identifier to properties
        foreach($this->yuccaIdentifier as $property=>$value) {
            $this->$property = $value;
            $this->yuccaInitialized[$property] = true;
        }

        return $this;
    }

    public function remove(){
        // Check that we have a mapping
        if(false === isset($this->yuccaMappingManager)){
            throw new \Exception("Mapping manager isn't set");
        }

        //Remove
        $this->yuccaMappingManager->getMapper(get_class($this))->remove($this->yuccaIdentifier);

        $this->reset(array());

        return $this;
    }

    public function __sleep() {
        return array_merge(
            array('yuccaInitialized', 'yuccaProperties', 'yuccaIdentifier'),
            $this->yuccaProperties
        );
    }
}
