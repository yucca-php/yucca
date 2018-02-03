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

use Wheeliz\Domain\Car\Entity\RentEntity;
use Yucca\Component\EntityManager;
use Yucca\Component\MappingManager;
use Yucca\Component\SelectorManager;

/**
 * Class ModelAbstract
 * @package Yucca\Model
 */
abstract class ModelAbstract implements ModelInterface
{
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
    protected $yuccaShardingKey;

    // For old PHP 5.3 (like 5.3.3) compatibility
    //abstract public function getId();

    /**
     * EntityManager
     * @param \Yucca\Component\MappingManager $mappingManager
     * @return ModelAbstract
     */
    public function setYuccaMappingManager(MappingManager $mappingManager)
    {
        $this->yuccaMappingManager = $mappingManager;

        return $this;
    }

    /**
     * @param \Yucca\Component\EntityManager $entityManager
     * @return ModelAbstract
     */
    public function setYuccaEntityManager(EntityManager $entityManager)
    {
        $this->yuccaEntityManager = $entityManager;

        return $this;
    }

    /**
     * @param \Yucca\Component\SelectorManager $selectorManager
     * @return ModelAbstract
     */
    public function setYuccaSelectorManager(SelectorManager $selectorManager)
    {
        $this->yuccaSelectorManager = $selectorManager;

        return $this;
    }

    /**
     * @param MappingManager  $mappingManager
     * @param SelectorManager $selectorManager
     * @param EntityManager   $entityManager
     *
     * @return mixed
     */
    public function refresh(MappingManager $mappingManager, SelectorManager $selectorManager, EntityManager $entityManager)
    {
        $this->yuccaMappingManager = $mappingManager;
        $this->yuccaSelectorManager = $selectorManager;
        $this->yuccaEntityManager = $entityManager;

        foreach (array_keys($this->yuccaInitialized) as $propertyName) {
            if ($this->$propertyName instanceof ModelInterface) {
                $this->yuccaEntityManager->refresh($this->$propertyName);
            }
        }

        return $this;
    }

    /**
     * @param mixed $identifier
     * @param null  $shardingKey
     *
     * @return $this
     */
    public function setYuccaIdentifier($identifier, $shardingKey = null)
    {
        $this->yuccaIdentifier = $identifier;
        $this->yuccaShardingKey = $shardingKey;

        return $this;
    }

    /**
     * @param mixed $identifier
     *
     * @return $this
     */
    public function reset($identifier)
    {
        $this->yuccaIdentifier = $identifier;
        foreach ($this->yuccaProperties as $propertyName) {
            $this->$propertyName = null;
        }
        $this->yuccaInitialized = array();

        return $this;
    }

    /**
     * @return $this
     * @throws \Yucca\Component\Source\Exception\NoDataException
     */
    public function ensureExist()
    {
        if (empty($this->yuccaIdentifier)) {
            throw new \Yucca\Component\Source\Exception\NoDataException(get_class($this).' doesn\'t seems to be saved in database');
        }
        try {
            foreach ($this->yuccaProperties as $propertyName) {
                if (false === isset($this->yuccaInitialized[$propertyName])) {
                    $this->hydrate($propertyName);
                }
            }
        } catch (\Yucca\Component\Source\Exception\NoDataException $exception) {
            throw new \Yucca\Component\Source\Exception\NoDataException(get_class($this).' doesn\'t seems to exist with identifiers : '.var_export($this->yuccaIdentifier, true));
        }

        return $this;
    }

    /**
     * @return ModelAbstract
     * @throws \Exception
     */
    public function save()
    {
        // Check that we have a mapping
        if (false === isset($this->yuccaMappingManager)) {
            throw new \Exception("Mapping manager isn't set");
        }

        //load values
        foreach ($this->yuccaProperties as $propertyName) {
            if (false === isset($this->yuccaInitialized[$propertyName])) {
                $this->hydrate($propertyName);
            }
        }

        //Create value list to save
        $toSave = array();
        foreach ($this->yuccaProperties as $propertyName) {
            $toSave[$propertyName] = $this->$propertyName;
        }
        //Save
        $mapper = $this->yuccaMappingManager->getMapper(get_class($this));
        $this->yuccaIdentifier = $mapper->save($this->yuccaIdentifier, $toSave, $this->yuccaShardingKey);

        //Set identifier to properties
        foreach ($this->yuccaIdentifier as $property => $value) {
            $this->$property = $value;
            $this->yuccaInitialized[$property] = true;
        }

        //Unset some properties dynamically set by DB
        foreach ($mapper->getPropertiesToRefreshAfterSave() as $property) {
            $this->$property = null;
            unset($this->yuccaInitialized[$property]);
        }

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function remove()
    {
        // Check that we have a mapping
        if (false === isset($this->yuccaMappingManager)) {
            throw new \Exception("Mapping manager isn't set");
        }

        //Remove
        $this->yuccaMappingManager->getMapper(get_class($this))->remove($this->yuccaIdentifier, $this->yuccaShardingKey);

        $this->reset(array());

        return $this;
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return array_merge(
            array('yuccaInitialized', 'yuccaProperties', 'yuccaIdentifier', 'yuccaShardingKey'),
            $this->yuccaProperties
        );
    }

    /**
     * @param $properties
     * @return ModelAbstract
     */
    protected function setYuccaProperties(array $properties)
    {
        $this->yuccaProperties = $properties;

        return $this;
    }

    /**
     * Hydrate this model with information coming from the mapping manager
     * @param $propertyName
     * @return ModelAbstract
     */
    protected function hydrate($propertyName)
    {

        if (isset($this->yuccaMappingManager) && (false === isset($this->yuccaInitialized[$propertyName])) && (false === empty($this->yuccaIdentifier))) {
            $values = $this->yuccaMappingManager->getMapper(get_class($this))->load($this->yuccaIdentifier, $propertyName, $this->yuccaShardingKey);
            foreach ($values as $property => $value) {
                if (false === isset($this->yuccaInitialized[$property])) {
                    $this->$property = $value;
                    $this->yuccaInitialized[$property] = true;
                }
            }
        }
        $this->yuccaInitialized[$propertyName] = true;

        return $this;
    }
}
