<?php
namespace Yucca\Component\Source\DataParser;

/**
 * Interface ParserInterface
 * @package Yucca\Component\Source\DataParser
 */
interface ParserInterface
{
    /**
     * @param string $fieldName
     * @param string $fieldValue
     * @param array  $values
     * @param array  $fieldConfiguration
     *
     * @return mixed
     */
    public function decode($fieldName, $fieldValue, array $values, array $fieldConfiguration);

    /**
     * @param string $fieldName
     * @param string $fieldValue
     * @param array  $values
     * @param array  $fieldConfiguration
     *
     * @return mixed
     */
    public function encode($fieldName, $fieldValue, array $values, array $fieldConfiguration);
}
