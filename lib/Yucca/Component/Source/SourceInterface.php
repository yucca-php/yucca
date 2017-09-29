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
 * Interface SourceInterface
 * @package Yucca\Component\Source
 */
interface SourceInterface
{
    /**
     * @param string $field
     *
     * @return mixed
     */
    public function canHandle($field);

    /**
     * @param string $field
     *
     * @return mixed
     */
    public function isIdentifier($field);

    /**
     * @param array $identifier
     * @param bool  $rawData
     * @param mixed $shardingKey
     *
     * @return mixed
     */
    public function load(array $identifier, $rawData, $shardingKey);

    /**
     * @param array $identifier
     * @param mixed $shardingKey
     *
     * @return mixed
     */
    public function remove(array $identifier, $shardingKey);

    /**
     * @param array $datas
     * @param array $identifier
     * @param null  $shardingKey
     * @param null  $affectedRows
     *
     * @return mixed
     */
    public function save($datas, array $identifier = array(), $shardingKey = null, &$affectedRows = null);

    /**
     * @param array $datas
     * @param array $identifier
     * @param null  $shardingKey
     * @param null  $affectedRows
     *
     * @return mixed
     */
    public function saveAfterLoading($datas, array $identifier = array(), $shardingKey = null, &$affectedRows = null);
}
