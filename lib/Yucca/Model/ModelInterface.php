<?php

namespace Yucca\Model;

use Yucca\Component\MappingManager;
use Yucca\Component\EntityManager;
use Yucca\Component\SelectorManager;

interface ModelInterface {
    public function getId();

    /**
     * EntityManager
     * @param \Yucca\Component\MappingManager $mappingManager
     * @return ModelInterface
     */
    public function setYuccaMappingManager(MappingManager $mappingManager);

    /**
     * @param \Yucca\Component\EntityManager $entityManager
     * @return ModelInterface
     */
    public function setYuccaEntityManager(EntityManager $entityManager);

    /**
     * @param \Yucca\Component\SelectorManager $selectorManager
     * @return ModelInterface
     */
    public function setYuccaSelectorManager(SelectorManager $selectorManager);

    /**
     * @param $identifier
     * @return ModelInterface
     */
    public function setYuccaIdentifier($identifier);

    /**
     * @param $identifier
     * @return ModelInterface
     */
    public function reset($identifier);

    /**
     * @throws \Yucca\Component\Source\Exception\NoDataException
     */
    public function ensureExist();

    /**
     * @return ModelInterface
     * @throws \Exception
     */
    public function save();

    /**
     * @return mixed
     */
    public function remove();
}
