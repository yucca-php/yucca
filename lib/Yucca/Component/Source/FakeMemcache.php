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

use Yucca\Component\Source\Exception\NoDataException;

/**
 * Class FakeMemcache
 * @package Yucca\Component\Source
 */
class FakeMemcache extends SourceAbstract
{
    /**
     * @param array $identifier
     * @param bool  $rawData
     * @param mixed $shardingKey
     *
     * @throws NoDataException
     */
    public function load(array $identifier, $rawData, $shardingKey)
    {
        throw new NoDataException("No datas found in fake cache");
    }

    /**
     * @param array $identifier
     * @param null  $shardingKey
     *
     * @return $this
     */
    public function remove(array $identifier, $shardingKey = null)
    {
        return $this;
    }

    /**
     * @param mixed $serializedCriterias
     * @param array $options
     * @throws Exception\NoDataException
     */
    public function loadIds($serializedCriterias, array $options = array())
    {
        throw new NoDataException("No datas found in fake cache");
    }

    /**
     * @param array $datas
     * @param array $identifier
     * @param null  $shardingKey
     * @param null  $affectedRows
     */
    public function save($datas, array $identifier = array(), $shardingKey = null, &$affectedRows = null)
    {
    }

    /**
     * @param array $datas
     * @param array $identifier
     * @param null  $shardingKey
     * @param null  $affectedRows
     */
    public function saveAfterLoading($datas, array $identifier = array(), $shardingKey = null, &$affectedRows = null)
    {
    }
}
