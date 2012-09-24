<?php
namespace Yucca\Component\Selector\Source;

interface SelectorSourceInterface
{
    const ID_FIELD = 'idField';
    const SHARDING_KEY_FIELD = 'shardingKeyField';
    const TABLE = 'table';
    const RESULT = 'result';
    const RESULT_COUNT = 'count';
    const RESULT_IDENTIFIERS = 'identifiers';
    const CONNECTION_NAME = 'connection_name';
    const SELECTOR_NAME = 'selector_name';
    const LIMIT = 'limit';
    const ORDERBY = 'orderBy';
    const FORCE_FROM_MASTER = 'force_from_master';

    public function loadIds(array $criterias, array $options=array());
    public function saveIds(array $ids, array $criterias, array $options=array());
    public function invalidateGlobal(array $options = array());
}
