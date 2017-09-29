<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Component\Source\DataParser;

use Yucca\Component\EntityManager;

/**
 * Class DataParser
 * @package Yucca\Component\Source\DataParser
 */
class DataParser
{
    /**
     * @var \Yucca\Component\EntityManager
     */
    protected $entityManager;

    /**
     * @param \Yucca\Component\EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param array $datas
     * @param array $fieldsConfiguration
     * @throws \Exception
     * @return array
     */
    public function decode(array $datas, array $fieldsConfiguration)
    {
        //TODO : We may have to introduce factories for sharded objects for example
        $toReturn = array();
        foreach ($datas as $dataKey => $dataValue) {
            if (isset($fieldsConfiguration[$dataKey]['type'])) {
                switch ($fieldsConfiguration[$dataKey]['type']) {
                    //Not sharded
                    case 'object':
                        if (is_null($dataValue)) {
                            $toReturn[$dataKey] = null;
                        } else {
                            if (false === isset($fieldsConfiguration[$dataKey]['class_name'])) {
                                throw new \Exception('Missing "class_name" for object dependency');
                            }
                            if (isset($fieldsConfiguration[$dataKey]['id_property_name'])) {
                                $id = array($fieldsConfiguration[$dataKey]['id_property_name'] => $dataValue);
                            } else {
                                $id = $dataValue;
                            }
                            $toReturn[$dataKey] = $this->entityManager->load($fieldsConfiguration[$dataKey]['class_name'], $id);
                        }

                        break;
                    case 'date':
                    case 'datetime':
                    case 'time':
                        if (is_null($dataValue)) {
                            $toReturn[$dataKey] = null;
                        } else {
                            $toReturn[$dataKey] = new \DateTime($dataValue);
                        }
                        break;
                    case 'boolean':
                        if (is_null($dataValue)) {
                            $toReturn[$dataKey] = null;
                        } else {
                            $toReturn[$dataKey] = $dataValue ? true : false;
                        }
                        break;
                    case 'json':
                        $toReturn[$dataKey] = json_decode($dataValue);
                        break;
                    case 'json_assoc':
                        $toReturn[$dataKey] = json_decode($dataValue, true);
                        break;
                    case 'identifier':
                        if (false === isset($fieldsConfiguration[$dataKey]['class_name'])) {
                            $toReturn[$dataKey] = $dataValue;
                        } else {
                            if (isset($fieldsConfiguration[$dataKey]['id_property_name'])) {
                                $id = array($fieldsConfiguration[$dataKey]['id_property_name'] => $dataValue);
                            } else {
                                $id = $dataValue;
                            }
                            $toReturn[$dataKey] = $this->entityManager->load($fieldsConfiguration[$dataKey]['class_name'], $id);
                        }
                        break;
                    case 'scalar':
                    default:
                        $toReturn[$dataKey] = $dataValue;
                        break;
                }
            } else {
                $toReturn[$dataKey] = $dataValue;
            }
        }

        return $toReturn;
    }

    /**
     * @param array $datas
     * @param array $fieldsConfiguration
     * @throws \Exception
     * @return array
     */
    public function encode(array $datas, array $fieldsConfiguration)
    {
        //TODO : We may have to introduce factories for sharded objects for example
        $toReturn = array();
        foreach ($datas as $dataKey => $dataValue) {
            if (isset($fieldsConfiguration[$dataKey]['type'])) {
                switch ($fieldsConfiguration[$dataKey]['type']) {
                    //Not sharded
                    case 'object':
                        if (is_null($dataValue)) {
                            $toReturn[$dataKey] = null;
                        } else {
                            if (false === is_object($dataValue)) {
                                throw new \Exception($dataKey.' is not an object');
                            }
                            if (false === isset($fieldsConfiguration[$dataKey]['class_name'])) {
                                throw new \Exception('Missing "class_name" for object dependency');
                            }
                            if (false === ($dataValue instanceof \Yucca\Model\ModelInterface)) {
                                throw new \Exception(sprintf('dataValue(%s) doesn\'t implement \Yucca\Model\ModelInterface'));
                            }
                            if (false === ($dataValue instanceof $fieldsConfiguration[$dataKey]['class_name'])) {
                                throw new \Exception(sprintf('dataValue(%s) is not an instance of configured class(%s)', get_class($dataValue), $fieldsConfiguration[$dataKey]['class_name']));
                            }
                            if (isset($fieldsConfiguration[$dataKey]['id_method_name'])) {
                                $idMethod = $fieldsConfiguration[$dataKey]['id_method_name'];
                            } else {
                                $idMethod = 'getId';
                            }
                            $dataValue->ensureExist();
                            $toReturn[$dataKey] = $dataValue->$idMethod();
                        }

                        break;
                    case 'date':
                        if ($dataValue instanceof \DateTime) {
                            $toReturn[$dataKey] = $dataValue->format('Y-m-d');
                        } else {
                            $toReturn[$dataKey] = $dataValue;
                        }
                        break;
                    case 'datetime':
                        if ($dataValue instanceof \DateTime) {
                            $toReturn[$dataKey] = $dataValue->format('Y-m-d H:i:s');
                        } else {
                            $toReturn[$dataKey] = $dataValue;
                        }
                        break;
                    case 'time':
                        if ($dataValue instanceof \DateTime) {
                            $toReturn[$dataKey] = $dataValue->format('H:i:s');
                        } else {
                            $toReturn[$dataKey] = $dataValue;
                        }
                        break;
                    case 'boolean':
                        if (is_null($dataValue)) {
                            $toReturn[$dataKey] = null;
                        } else {
                            $toReturn[$dataKey] = ($dataValue ? 1 : 0);
                        }
                        break;
                    case 'json':
                    case 'json_assoc':
                        $toReturn[$dataKey] = json_encode($dataValue);
                        break;
                    case 'identifier':
                        if (false === isset($fieldsConfiguration[$dataKey]['class_name'])) {
                            $toReturn[$dataKey] = $dataValue;
                        } else {
                            if (false === is_object($dataValue)) {
                                throw new \Exception($dataKey.' is not an object');
                            }
                            if (false === ($dataValue instanceof \Yucca\Model\ModelInterface)) {
                                throw new \Exception(sprintf('dataValue(%s) doesn\'t implement \Yucca\Model\ModelInterface'));
                            }
                            if (false === ($dataValue instanceof $fieldsConfiguration[$dataKey]['class_name'])) {
                                throw new \Exception(sprintf('dataValue(%s) is not an instance of configured class(%s)', get_class($dataValue), $fieldsConfiguration[$dataKey]['class_name']));
                            }
                            if (isset($fieldsConfiguration[$dataKey]['id_method_name'])) {
                                $idMethod = $fieldsConfiguration[$dataKey]['id_method_name'];
                            } else {
                                $idMethod = 'getId';
                            }
                            $dataValue->ensureExist();
                            $toReturn[$dataKey] = $dataValue->$idMethod();
                        }
                        break;
                    case 'scalar':
                    default:
                        $toReturn[$dataKey] = $dataValue;
                        break;
                }
            } else {
                $toReturn[$dataKey] = $dataValue;
            }
        }

        return $toReturn;
    }
}
