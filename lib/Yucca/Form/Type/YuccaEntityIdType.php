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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Yucca\Component\EntityManager;
use Symfony\Component\Form\AbstractType;

class YuccaEntityIdType extends AbstractType
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public function configureOptions(OptionsResolver $resolver)
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
    public function getBlockPrefix()
    {
        return 'yucca_entity_id';
    }

    public function getParent()
    {
        return TextType::class;
    }
}
