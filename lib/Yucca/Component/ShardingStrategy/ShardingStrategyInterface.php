<?php
namespace Yucca\Component\ShardingStrategy;

interface ShardingStrategyInterface {
    function getShardingIdentifier(array $tableConfig, $shardingKey);
}