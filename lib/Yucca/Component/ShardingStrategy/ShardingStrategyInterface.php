<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Component\ShardingStrategy;

/**
 * Interface ShardingStrategyInterface
 * @package Yucca\Component\ShardingStrategy
 */
interface ShardingStrategyInterface
{
    /**
     * @param array $tableConfig
     * @param mixed $shardingKey
     *
     * @return mixed
     */
    public function getShardingIdentifier(array $tableConfig, $shardingKey);
}
