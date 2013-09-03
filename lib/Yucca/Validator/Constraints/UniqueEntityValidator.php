<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\ConstraintValidator;
use Yucca\Component\EntityManager;
use Yucca\Component\SelectorManager;
use Yucca\Model\ModelAbstract;

/**
 * Unique Entity Validator checks if one or a set of fields contain unique values.
 * Inspired by Doctrine UniqueEntityValidator by Benjamin Eberlei <kontakt@beberlei.de>
 */
class UniqueEntityValidator extends ConstraintValidator
{
    /**
     * @var SelectorManager
     */
    private $selectorManager;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param SelectorManager $selectorManager
     * @param \Yucca\Component\EntityManager $entityManager
     */
    public function __construct(SelectorManager $selectorManager, EntityManager $entityManager)
    {
        $this->selectorManager = $selectorManager;
        $this->entityManager = $entityManager;
    }

    /**
     * @param ModelAbstract $entity
     * @param Constraint $constraint
     *
     * @throws UnexpectedTypeException
     * @throws ConstraintDefinitionException
     */
    public function validate($entity, Constraint $constraint)
    {
        if(false === ($entity instanceof ModelAbstract )) {
            throw new UnexpectedTypeException($entity, 'ModelAbstract');
        }
        /**
         * @var ModelAbstract $entity
         */

        if (!is_array($constraint->fields) && !is_string($constraint->fields)) {
            throw new UnexpectedTypeException($constraint->fields, 'array');
        }

        if (null !== $constraint->errorPath && !is_string($constraint->errorPath)) {
            throw new UnexpectedTypeException($constraint->errorPath, 'string or null');
        }

        $fields = (array) $constraint->fields;

        if (0 === count($fields)) {
            throw new ConstraintDefinitionException('At least one field has to be specified.');
        }

        if(!$constraint->selector){
            $selectorName = str_replace('Model','Selector',get_class($entity));
        } else {
            $selectorName = $constraint->selector;
        }

        $selector = $this->selectorManager->getSelector($selectorName);
        $selectorMethods = get_class_methods($selectorName);

        $criteria = array();
        foreach ($fields as $fieldName) {
            $camelFieldName = str_replace(' ','',ucwords(str_replace('_',' ',$fieldName)));
            $selectorMethod = 'add'.$camelFieldName.'Criteria';
            if (false === in_array($selectorMethod,$selectorMethods)) {
                throw new ConstraintDefinitionException(sprintf("The field '%s' is not mapped", $fieldName));
            }

            $getterMethod = 'get'.$camelFieldName;
            $value = $entity->$getterMethod();

            if ($constraint->ignoreNull && null === $value) {
                return;
            }

            $selector->$selectorMethod($value);
        }
        
        $iterator = new \Yucca\Component\Iterator\Iterator(
            $selector,
            $this->entityManager,
            get_class($entity)
        );

        /* If no entity matched the query criteria or a single entity matched,
         * which is the same as the entity being validated, the criteria is
         * unique.
         */

        if (0 == $iterator->count() || (1 == $iterator->count() && $entity->getId() == ($iterator->current()->getId()))) {
            return;
        }

        $errorPath = null !== $constraint->errorPath ? $constraint->errorPath : $fields[0];

        $this->context->addViolationAt($errorPath, $constraint->message, array(), $value);
    }
}
