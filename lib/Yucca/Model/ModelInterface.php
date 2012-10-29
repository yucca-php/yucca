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
     * @return ModelAbstract
     */
    public function setYuccaMappingManager(MappingManager $mappingManager);

    /**
     * @param \Yucca\Component\EntityManager $entityManager
     * @return ModelAbstract
     */
    public function setYuccaEntityManager(EntityManager $entityManager);

    /**
     * @param \Yucca\Component\SelectorManager $selectorManager
     * @return ModelAbstract
     */
    public function setYuccaSelectorManager(SelectorManager $selectorManager);

    /**
     * @param $identifier
     * @return ModelAbstract
     */
    public function setYuccaIdentifier($identifier);

    /**
     * @param $identifier
     * @return ModelAbstract
     */
    public function reset($identifier);

    /**
     * @throws \Yucca\Component\Source\Exception\NoDataException
     */
    public function ensureExist();

    /**
     * @return ModelAbstract
     * @throws \Exception
     */
    public function save();

    /**
     * @return mixed
     */
    public function remove();
}
