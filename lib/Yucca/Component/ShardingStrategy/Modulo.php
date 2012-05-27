<?php
namespace Yucca\Component\ShardingStrategy;

class Modulo implements ShardingStrategyInterface{
    public function getShardingIdentifier(array $tableConfig, $shardingKey) {
        if(is_array($tableConfig['shards']) && count($tableConfig['shards'])) {
            return $shardingKey % count($tableConfig['shards']);
        }

        throw new \Exception("No shards defined : can't compute modulo");
    }
}