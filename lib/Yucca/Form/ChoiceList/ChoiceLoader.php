<?php
namespace Yucca\Form\ChoiceList;

use Symfony\Component\Form\ChoiceList\Factory\ChoiceListFactoryInterface;
use Symfony\Component\Form\ChoiceList\Factory\DefaultChoiceListFactory;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Yucca\Component\EntityManager;
use Yucca\Component\Iterator\Iterator;

/**
 * Class ChoiceLoader
 * @package Yucca\Form\ChoiceList
 */
class ChoiceLoader implements ChoiceLoaderInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Contains the query builder that builds the query for fetching the
     * entities
     *
     * This property should only be accessed through queryBuilder.
     *
     * @var Iterator
     */
    private $iterator;

    /**
     * The preferred entities.
     *
     * @var array
     */
    private $preferredEntities = array();

    private $modelClassName;
    private $selectorClassName;

    private $choiceList;

    private $factory;
    /**
     * Creates a new entity choice list.
     *
     * @param EntityManager             $manager           An EntityManager instance
     * @param string                    $modelClassName   The model class name
     * @param string                    $selectorClassName The selector class name
     * @param string                    $labelPath         The property path used for the label
     * @param Iterator                  $iterator          An optional query builder
     * @param array                     $entities          An array of choices
     * @param array                     $preferredEntities An array of preferred choices
     * @param string                    $groupPath         A property path pointing to the property used
     *                                                     to group the choices. Only allowed if
     *                                                     the choices are given as flat array.
     * @param PropertyAccessorInterface $propertyAccessor The reflection graph for reading property paths.
     */
    public function __construct(EntityManager $manager, $modelClassName, $selectorClassName, $labelPath = null, $iterator = null, array $preferredEntities = array(), $groupPath = null, ChoiceListFactoryInterface $factory)
    {
        $this->entityManager = $manager;
        $this->modelClassName = $modelClassName;
        $this->selectorClassName = $selectorClassName;
        $this->iterator = $iterator;
        $this->preferredEntities = $preferredEntities;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function loadChoiceList($value = null)
    {
        if(is_null($value)) {
            $value = function($choice){
                if($choice instanceof \Yucca\Model\ModelInterface) {
                    return $choice->getId();
                } else {
                    return (string) $choice;
                }
            };
        }

        if ($this->choiceList) {
            return $this->choiceList;
        }

        if ($this->iterator) {
            if(is_array($this->iterator)) {
                $entities = $this->iterator;
            } else {
                $entities = $this->iterator->getArray();
            }
        } else {
            $selector = $this->entityManager->getSelectorManager()->getSelector($this->selectorClassName);
            $iterator = new Iterator(
                $selector,
                $this->entityManager,
                $this->modelClassName
            );
            $entities = $iterator->getArray();
        }

        $this->choiceList = $this->factory->createListFromChoices($entities, $value);

        return $this->choiceList;
    }

    /**
     * {@inheritdoc}
     */
    public function loadValuesForChoices(array $choices, $value = null)
    {
        // Performance optimization
        if (empty($choices)) {
            return array();
        }

        // Optimize performance for single-field identifiers. We already
        // know that the IDs are used as values

        // Attention: This optimization does not check choices for existence
//        if (!$this->choiceList && $this->idReader->isSingleId()) {
//            $values = array();
//
//            // Maintain order and indices of the given objects
//            foreach ($choices as $i => $object) {
//                if ($object instanceof $this->class) {
//                    // Make sure to convert to the right format
//                    $values[$i] = (string) $this->idReader->getIdValue($object);
//                }
//            }
//
//            return $values;
//        }

        return $this->loadChoiceList($value)->getValuesForChoices($choices);
    }

    /**
     * {@inheritdoc}
     */
    public function loadChoicesForValues(array $values, $value = null)
    {
        // Performance optimization
        // Also prevents the generation of "WHERE id IN ()" queries through the
        // object loader. At least with MySQL and on the development machine
        // this was tested on, no exception was thrown for such invalid
        // statements, consequently no test fails when this code is removed.
        // https://github.com/symfony/symfony/pull/8981#issuecomment-24230557
        if (empty($values)) {
            return array();
        }

        // Optimize performance in case we have an object loader and
        // a single-field identifier
//        if (!$this->choiceList && $this->objectLoader && $this->idReader->isSingleId()) {
//            $unorderedObjects = $this->objectLoader->getEntitiesByIds($this->idReader->getIdField(), $values);
//            $objectsById = array();
//            $objects = array();
//
//            // Maintain order and indices from the given $values
//            // An alternative approach to the following loop is to add the
//            // "INDEX BY" clause to the Doctrine query in the loader,
//            // but I'm not sure whether that's doable in a generic fashion.
//            foreach ($unorderedObjects as $object) {
//                $objectsById[$this->idReader->getIdValue($object)] = $object;
//            }
//
//            foreach ($values as $i => $id) {
//                if (isset($objectsById[$id])) {
//                    $objects[$i] = $objectsById[$id];
//                }
//            }
//
//            return $objects;
//        }

        return $this->loadChoiceList($value)->getChoicesForValues($values);
    }
}
