<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Component\Source;

/**
 * Class SourceAbstract
 * @package Yucca\Component\Source
 */
abstract class SourceAbstract implements SourceInterface
{
    protected $configuration;
    protected $sourceName;

    /**
     * Constructor
     * @param string $sourceName
     * @param array  $configuration
     */
    public function __construct($sourceName, array $configuration = array())
    {
        $this->configuration = $configuration;
        $this->sourceName = $sourceName;
    }

    /**
     * @param string $fieldName
     * @return bool
     */
    public function canHandle($fieldName)
    {
        return array_key_exists($fieldName, $this->configuration['fields']);
    }

    /**
     * @param string $fieldName
     * @return bool
     */
    public function isIdentifier($fieldName)
    {
        return isset($this->configuration['fields'][$fieldName]['type']) && ('identifier' === $this->configuration['fields'][$fieldName]['type']);
    }
}
