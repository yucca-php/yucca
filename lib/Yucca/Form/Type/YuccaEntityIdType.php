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

use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Yucca\Component\EntityManager;
use Symfony\Component\Form\AbstractType;

class YuccaEntityIdType extends AbstractType
{
    /**
     * @var EntityManager
     */
    protected $entityManager;
    /**
     * @var string
     */
    protected $parent;
    /**
     * @var string
     */
    protected $name;

    public function __construct(EntityManager $entityManager, $name = 'yucca_entity_id', $parent='text')
    {
        $this->entityManager = $entityManager;
        $this->name = $name;
        $this->parent = $parent;
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'model_class_name'  => null,
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entityManager = $this->entityManager;

        $builder
            ->addViewTransformer(
                new CallbackTransformer(
                    function ($data) {
                        if(empty($data)) {
                            return null;
                        }
                        return $data->getId();
                    },
                    function ($data) use ($entityManager, $options) {
                        if(empty($data)) {
                            return null;
                        }
                        return $entityManager->load($options['model_class_name'], $data)->ensureExist();
                    }
                ),
                true
            )
        ;
    }
    public function getName()
    {
        return $this->name;
    }

    public function getParent()
    {
        return $this->parent;
    }
}
