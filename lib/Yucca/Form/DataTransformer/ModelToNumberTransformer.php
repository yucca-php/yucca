<?php
namespace Yucca\Form\DataTransformer;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Yucca\Component\EntityManager;
use Yucca\Model\ModelInterface;

/**
 * Class ModelToNumberTransformer
 *
 * @package Yucca\Form\DataTransformer
 */
class ModelToNumberTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManager
     */
    private $yuccaEntityManager;

    /**
     * @param EntityManager $yuccaEntityManager
     * @param string        $modelClassName
     */
    public function __construct(EntityManager $yuccaEntityManager, $modelClassName)
    {
        $this->yuccaEntityManager = $yuccaEntityManager;
        $this->modelClassName = $modelClassName;
    }

    /**
     * Transforms an object to a string (number).
     *
     * @param ModelInterface|null $shareType
     *
     * @return string
     */
    public function transform($shareType)
    {
        if (empty($shareType)) {
            return "";
        }

        return $shareType->getId();
    }

    /**
     * Transforms a string (number) to an object.
     *
     * @param string $number
     *
     * @return ModelInterface |null
     *
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($number)
    {
        if (!$number) {
            return null;
        }

        try {
            $shareType = $this->yuccaEntityManager->load($this->modelClassName, $number);
        } catch (\Exception $e) {
            throw new TransformationFailedException(sprintf(
                'The object can\'t be found',
                $number
            ));
        }

        return $shareType;
    }
}
