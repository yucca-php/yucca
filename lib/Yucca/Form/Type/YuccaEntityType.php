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

use Symfony\Component\Form\ChoiceList\Factory\DefaultChoiceListFactory;
use Symfony\Component\Form\ChoiceList\Factory\PropertyAccessDecorator;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Yucca\Component\EntityManager;
use Yucca\Form\ChoiceList\ChoiceLoader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Yucca\Form\DataTransformer\IteratorToArrayTransformer;

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
     * @var PropertyAccessDecorator
     */
    private $choiceListFactory;

    /**
     * @param EntityManager             $entityManager
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function __construct(EntityManager $entityManager, PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->entityManager = $entityManager;
        $this->choiceListFactory = $propertyAccessor ?: new PropertyAccessDecorator(new DefaultChoiceListFactory(), $propertyAccessor);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['multiple']) {
            $builder->addModelTransformer(new IteratorToArrayTransformer(), true);
        }
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @return mixed
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $choiceListCache =& $this->choiceListCache;
        $choiceListFactory = $this->choiceListFactory;
        $entityManager = $this->entityManager;

        $choiceLoader = function (Options $options) use (&$choiceListCache, $choiceListFactory, $entityManager) {
            // Support for closures
            $propertyHash = is_object($options['choice_label'])
                ? spl_object_hash($options['choice_label'])
                : $options['choice_label'];
            $iteratorHash = is_object($options['iterator'])
                ? spl_object_hash($options['iterator'])
                : $options['iterator'];

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
                $preferredChoiceHashes,
                $groupByHash,
                $iteratorHash
            )));

            if (!isset($choiceListCache[$hash])) {
                $choiceListCache[$hash] = new ChoiceLoader(
                    $entityManager,
                    $options['model_class_name'],
                    $options['selector_class_name'],
                    $options['choice_label'],
                    $options['iterator'],
                    $options['preferred_choices'],
                    $options['group_by'],
                    $choiceListFactory
                );
            }

            return $choiceListCache[$hash];
        };

        $resolver->setDefaults(array(
            'em'                => null,
            'query_builder'     => null,
            'choices'           => array(),
            'choice_loader'     => $choiceLoader,
            'group_by'          => null,
            'iterator'          => null,
            'model_class_name'  => null,
            'selector_class_name' => null,
            //'choices_as_values' => true,
        ));

        $resolver->setRequired(array('model_class_name'));

        $resolver->setAllowedTypes('iterator', array('null', 'Yucca\Component\Iterator\Iterator', 'array'));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'yucca_entity';
    }

    /**
     * @return null|string|\Symfony\Component\Form\FormTypeInterface
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
