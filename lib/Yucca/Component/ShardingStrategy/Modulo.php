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

class Modulo implements ShardingStrategyInterface
{
    public function getShardingIdentifier(array $tableConfig, $shardingKey)
    {
        is_array($tableConfig['shards']) or $tableConfig['shards'] = array();

        $count = count($tableConfig['shards']);

        $toReturn = null;

        switch($count) {
            case 0:
                throw new \Exception("No shards defined : can't compute modulo");
                break;
            case 1:
                return null;
                break;

            default:
                $toReturn = $shardingKey % count($tableConfig['shards']);
                break;
        }

        return $toReturn;

    }
}
