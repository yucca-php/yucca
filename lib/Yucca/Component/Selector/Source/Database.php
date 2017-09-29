<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Component\Selector\Source;

use Yucca\Component\SchemaManager;

/**
 * Class Database
 * @package Yucca\Component\Selector\Source
 */
class Database implements SelectorSourceInterface
{

    /**
     * @var \Yucca\Component\SchemaManager
     */
    protected $schemaManager;

    /**
     * @param SchemaManager $schemaManager
     */
    public function setSchemaManager($schemaManager)
    {
        $this->schemaManager = $schemaManager;
    }

    /**
     * @param array $criterias
     * @param array $options
     * @throws \Exception
     * @throws \Yucca\Component\Selector\Exception\NoDataException
     * @return string
     */
    public function loadIds(array $criterias, array $options = array())
    {
        //Merge options
        $defaultOptions = array(
            SelectorSourceInterface::ID_FIELD => array('id'),
            SelectorSourceInterface::FORCE_FROM_MASTER => false,
            SelectorSourceInterface::SHARDING_KEY_FIELD => '',
            SelectorSourceInterface::TABLE => '',
            SelectorSourceInterface::RESULT => SelectorSourceInterface::RESULT_IDENTIFIERS,
        );
        $options = array_merge($defaultOptions, $options);

        //Check options
        if (empty($options[SelectorSourceInterface::TABLE])) {
            throw new \Exception('Table must be set for selector source');
        }
        if (empty($options[SelectorSourceInterface::ID_FIELD])) {
            throw new \Exception('Id Field must be set for selector source');
        }

        //fields
        if (self::RESULT_COUNT === $options[SelectorSourceInterface::RESULT]) {
            $securedFields = array();
            foreach ($options[SelectorSourceInterface::ID_FIELD] as $optionFieldName) {
                if ('*' == $optionFieldName) {
                    $securedFields[] = '*';
                } else {
                    $fullyQualifiedFieldName = explode(' as ', $optionFieldName);
                    $securedFields[] = $fullyQualifiedFieldName[0];
                }
            }
            $fields = array("COUNT(".implode(',', $securedFields).")");
        } elseif (self::RESULT_IDENTIFIERS === $options[SelectorSourceInterface::RESULT]) {
            if (empty($options[SelectorSourceInterface::SHARDING_KEY_FIELD])) {
                $fields = $options[SelectorSourceInterface::ID_FIELD];
            } else {
                $fields = array_merge($options[SelectorSourceInterface::ID_FIELD], array($options[SelectorSourceInterface::SHARDING_KEY_FIELD]));
            }
        } else {
            throw new \Exception('Unknown result type');
        }

        $result = $this->schemaManager->fetchIds(
            $options[SelectorSourceInterface::TABLE],
            $criterias,
            $fields,
            isset($options[SelectorSourceInterface::SHARDING_KEY]) ? $options[SelectorSourceInterface::SHARDING_KEY] : null,
            $options[SelectorSourceInterface::FORCE_FROM_MASTER],
            $options
        );

        if (self::RESULT_COUNT === $options[SelectorSourceInterface::RESULT]) {
            if (false === is_array($result) || false === is_array(current($result))) {
                return 0;
            }

            return current(current($result));
        } else {
            return $result;
        }
    }

    /**
     * @param array $ids
     * @param array $criterias
     * @param array $options
     *
     * @throws \Exception
     */
    public function saveIds($ids, array $criterias, array $options = array())
    {
        throw new \Exception("Database selector source can't save result");
    }

    /**
     * @param array $options
     */
    public function invalidateGlobal(array $options = array())
    {
    }
}
