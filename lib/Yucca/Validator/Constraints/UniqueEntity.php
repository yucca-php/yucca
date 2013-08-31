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

/**
 * Unique Entity Validator checks if one or a set of fields contain unique values.
 * Inspired by Doctrine UniqueEntityValidator by Benjamin Eberlei <kontakt@beberlei.de>
 * @Annotation
 */
class UniqueEntity extends Constraint
{
    public $message = 'This value is already used.';
    public $service = 'yucca.validator.unique';
    public $selector = null;
    public $fields = array();
    public $errorPath = null;
    public $ignoreNull = true;

    public function getRequiredOptions()
    {
        return array('fields');
    }

    /**
     * The validator must be defined as a service with this name.
     *
     * @return string
     */
    public function validatedBy()
    {
        return $this->service;
    }

    /**
     * {@inheritDoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function getDefaultOption()
    {
        return 'fields';
    }
}
