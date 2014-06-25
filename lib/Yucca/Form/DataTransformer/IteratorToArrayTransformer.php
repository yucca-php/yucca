<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Form\DataTransformer;

use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\DataTransformerInterface;
use Yucca\Component\Iterator\Iterator;

/**
 * Class IteratorToArrayTransformer
 *
 * @package Yucca\Form\DataTransformer
 */
class IteratorToArrayTransformer implements DataTransformerInterface
{
    /**
     * Transforms a yucca iterator into an array.
     *
     * @param Iterator $iterator A collection of entities
     *
     * @return mixed An array of entities
     *
     * @throws TransformationFailedException
     */
    public function transform($iterator)
    {
        if (null === $iterator) {
            return array();
        }

        if (is_array($iterator)) {
            return $iterator;
        }

        if (!$iterator instanceof Iterator) {
            throw new TransformationFailedException('Expected a Yucca\Component\Iterator\Iterator object.');
        }

        return $iterator->getArray();
    }

    /**
     * Transforms choice keys into Iterator.
     *
     * @param mixed $array An array of entities
     *
     * @return An iterator of entities
     */
    public function reverseTransform($array)
    {
        if ('' === $array || null === $array) {
            $array = array();
        } else {
            $array = (array) $array;
        }

        return $array;
    }
}
