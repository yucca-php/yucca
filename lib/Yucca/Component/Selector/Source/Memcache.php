<?php
namespace Yucca\Component\Selector\Source;

use Yucca\Component\ConnectionManager;
use Yucca\Component\Selector\Exception\NoDataException;

class Memcache implements SelectorSourceInterface{

    /**
     * @var \Yucca\Component\ConnectionManager
     */
    protected $connectionManager;

    public function setConnectionManager(ConnectionManager $connectionManager){
        $this->connectionManager = $connectionManager;
    }

    protected function getCacheKey(array $criterias, array $options = array()){
        //fields
        if (SelectorSourceInterface::RESULT_COUNT === $options[SelectorSourceInterface::RESULT]) {
            $suffix = 'count';
        } elseif (SelectorSourceInterface::RESULT_IDENTIFIERS === $options[SelectorSourceInterface::RESULT]) {
            $suffix = 'content';
        } else {
            throw new \Exception('Unknown result type');
        }

        $cacheKey = array();
        foreach($criterias as $criteriaKey=>$criteriaValue){
            if(false === is_array($criteriaValue)){
                $criteriaValue = array($criteriaValue);
            }

            foreach($criteriaValue as $v){
                if($v instanceof \Yucca\Model\ModelAbstract) {
                    $cacheKey[] = $criteriaKey.'-'.$v->getId().'-'.$v->getUpdatedAt();
                } elseif(is_scalar($v)) {
                    $cacheKey[] = $criteriaKey.'-'.$v;
                } elseif($criteriaValue instanceof \Yucca\Component\Selector\Expression) {
                    $whereCriterias[] = $criteriaValue->toString('memcache');
                } else {
                    throw new \Exception("Can't use criteria $criteriaKey");
                }
            }
        }

        return $options[SelectorSourceInterface::SELECTOR_NAME].':'.md5(implode(':',$cacheKey)).':'.$suffix;
    }

    protected function mergeOptions(array $options){
        //Merge options
        $defaultOptions = array(
            SelectorSourceInterface::RESULT => SelectorSourceInterface::RESULT_IDENTIFIERS,
            SelectorSourceInterface::CONNECTION_NAME => '',
            SelectorSourceInterface::SELECTOR_NAME => '',
        );

        $options = array_merge($defaultOptions, $options);

        if(empty($options[SelectorSourceInterface::CONNECTION_NAME])){
            throw new \Exception('A connection name must be given to the selector source');
        }

        if(empty($options[SelectorSourceInterface::SELECTOR_NAME])){
            throw new \Exception('A selector name must be given to the selector source');
        }

        return $options;
    }

    /**
     * @param array $criterias
     * @param array $options
     * @return string
     * @throws \Exception
     */
    public function loadIds(array $criterias, array $options = array()) {
        $options = $this->mergeOptions($options);

        $connection = $this->connectionManager->getConnection($options[SelectorSourceInterface::CONNECTION_NAME]);
        $result = $connection->get( $this->getCacheKey($criterias, $options) );

        if(false === $result){
            throw new NoDataException('No data');
        } else {
            return $result;
        }
    }

    public function saveIds(array $ids, array $criterias, array $options = array()) {
        $options = $this->mergeOptions($options);

        $connection = $this->connectionManager->getConnection($options[SelectorSourceInterface::CONNECTION_NAME]);
        return $connection->set( $this->getCacheKey($criterias, $options) , $ids, null, 0);
    }

    public function invalidateGlobal(array $options = array()){
        $connection = $this->connectionManager->getConnection($options[SelectorSourceInterface::CONNECTION_NAME]);
        $connection->delete( $this->getCacheKey(array(), array_merge($options, array(SelectorSourceInterface::RESULT => SelectorSourceInterface::RESULT_IDENTIFIERS))));
        $connection->delete( $this->getCacheKey(array(), array_merge($options, array(SelectorSourceInterface::RESULT => SelectorSourceInterface::RESULT_COUNT))));
    }
}