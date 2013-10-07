<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Yucca\Component\EntityManager;
use Yucca\Form\ChoiceList\EntityChoiceList;
/*use Symfony\Bridge\Doctrine\Form\EventListener\MergeDoctrineCollectionListener;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;*/
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Unique Entity Validator checks if one or a set of fields contain unique values.
 * Inspired by Doctrine Bridge bundle
 */
class YuccaEntityType extends AbstractType
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var array
     */
    private $choiceListCache = array();

    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    public function __construct(EntityManager $entityManager, PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->entityManager = $entityManager;
        $this->propertyAccessor = $propertyAccessor ?: PropertyAccess::getPropertyAccessor();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*if ($options['multiple']) {
            $builder
                ->addEventSubscriber(new MergeDoctrineCollectionListener())
                ->addViewTransformer(new CollectionToArrayTransformer(), true)
            ;
        }*/
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $choiceListCache =& $this->choiceListCache;
        $propertyAccessor = $this->propertyAccessor;
        $entityManager = $this->entityManager;

        $choiceList = function (Options $options) use (&$choiceListCache, $propertyAccessor, $entityManager) {
            // Support for closures
            $propertyHash = is_object($options['property'])
                ? spl_object_hash($options['property'])
                : $options['property'];

            $choiceHashes = $options['choices'];

            // Support for recursive arrays
            if (is_array($choiceHashes)) {
                // A second parameter ($key) is passed, so we cannot use
                // spl_object_hash() directly (which strictly requires
                // one parameter)
                array_walk_recursive($choiceHashes, function (&$value) {
                    $value = spl_object_hash($value);
                });
            }

            $preferredChoiceHashes = $options['preferred_choices'];

            if (is_array($preferredChoiceHashes)) {
                array_walk_recursive($preferredChoiceHashes, function (&$value) {
                    $value = spl_object_hash($value);
                });
            }

            // Support for closures
            $groupByHash = is_object($options['group_by'])
                ? spl_object_hash($options['group_by'])
                : $options['group_by'];

            $hash = md5(json_encode(array(
                spl_object_hash($entityManager),
                $options['model_class_name'],
                $options['selector_class_name'],
                $propertyHash,
                $choiceHashes,
                $preferredChoiceHashes,
                $groupByHash
            )));

            if (!isset($choiceListCache[$hash])) {
                $choiceListCache[$hash] = new EntityChoiceList(
                    $entityManager,
                    $options['model_class_name'],
                    $options['selector_class_name'],
                    $options['property'],
                    $options['iterator'],
                    $options['choices'],
                    $options['preferred_choices'],
                    $options['group_by'],
                    $propertyAccessor
                );
            }

            return $choiceListCache[$hash];
        };

        $resolver->setDefaults(array(
            'em'                => null,
            'property'          => null,
            'query_builder'     => null,
            'choices'           => null,
            'choice_list'       => $choiceList,
            'group_by'          => null,
            'iterator'          => null,
            'model_class_name'  => null,
            'selector_class_name' => null,
        ));

        $resolver->setRequired(array('model_class_name'));

        $resolver->setAllowedTypes(array(
            'iterator' => array('null', 'Yucca\Component\Iterator\Iterator'),
        ));
    }

    public function getName()
    {
        return 'yucca_entity';
    }

    public function getParent()
    {
        return 'choice';
    }
}
