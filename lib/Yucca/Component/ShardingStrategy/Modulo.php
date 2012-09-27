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

class Modulo implements ShardingStrategyInterface{
    public function getShardingIdentifier(array $tableConfig, $shardingKey) {
        if(is_array($tableConfig['shards']) && count($tableConfig['shards'])) {
            return $shardingKey % count($tableConfig['shards']);
        }

        throw new \Exception("No shards defined : can't compute modulo");
    }
}
