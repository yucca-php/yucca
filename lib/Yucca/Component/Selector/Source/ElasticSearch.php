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

use Elastica\Query;
use Yucca\Component\ConnectionManager;

class ElasticSearch implements SelectorSourceInterface{

    /**
     * @var \Yucca\Component\ConnectionManager
     */
    protected $connectionManager;

    protected $resultSets;

    public function setConnectionManager(ConnectionManager $connectionManager){
        $this->connectionManager = $connectionManager;
    }

    protected function getResultSetKey($query, $limit, $offset, $orderBy, $facets) {
        $facetsParams = array();
        foreach($facets as $facet) {
            $facetsParams[] = $facet->getParams();
        }
        return md5(
            var_export($query->toArray(), true).
            $limit.
            $offset.
            var_export($orderBy, true).
            var_export($facetsParams, true)
        );
    }

    /**
     * @param array $criterias
     * @param array $options
     * @throws \Exception
     * @throws \Yucca\Component\Selector\Exception\NoDataException
     * @return string
     */
    public function loadIds(array $criterias, array $options = array()) {
        //Merge options
        $defaultOptions = array(
            SelectorSourceInterface::ID_FIELD => array('id'),
            SelectorSourceInterface::FORCE_FROM_MASTER => false,
            SelectorSourceInterface::SHARDING_KEY_FIELD => '',
            SelectorSourceInterface::TABLE => '',
            SelectorSourceInterface::CONNECTION_NAME => '',
            SelectorSourceInterface::LIMIT => 10,
            SelectorSourceInterface::OFFSET => 0,
            SelectorSourceInterface::ORDERBY => null,
            SelectorSourceInterface::FACETS => array(),
            SelectorSourceInterface::RESULT => SelectorSourceInterface::RESULT_IDENTIFIERS,
        );
        $options = array_merge($defaultOptions, $options);

        $resultSetKey = $this->getResultSetKey(
            $options[SelectorSourceInterface::ELASTIC_QUERY],
            $options[SelectorSourceInterface::LIMIT],
            $options[SelectorSourceInterface::OFFSET],
            $options[SelectorSourceInterface::ORDERBY],
            $options[SelectorSourceInterface::FACETS]
        );

        if(false === isset($this->resultSets[$resultSetKey])) {
            //Check options
            if(empty($options[SelectorSourceInterface::ELASTIC_SEARCHABLE])){
                throw new \Exception('Elastic searchable index or type must be set for selector source');
            }
            if(false === $options[SelectorSourceInterface::ELASTIC_SEARCHABLE] instanceof \Elastica\SearchableInterface){
                throw new \Exception('Elastic searchable must be an instance of \Elastica\SearchableInterface');
            }
            if(empty($options[SelectorSourceInterface::ID_FIELD])){
                throw new \Exception('Id Field must be set for selector source');
            }

            /**
             * @var $index \Elastica\SearchableInterface
             */
            $index = $options[SelectorSourceInterface::ELASTIC_SEARCHABLE];
            $query = $options[SelectorSourceInterface::ELASTIC_QUERY];
            $query = Query::create($query);

            if (self::RESULT_COUNT !== $options[SelectorSourceInterface::RESULT]) {
                if(is_numeric($options[SelectorSourceInterface::LIMIT])) {
                    $query->setSize(
                        (int)$options[SelectorSourceInterface::LIMIT]
                    );
                }
                if(is_numeric($options[SelectorSourceInterface::OFFSET])) {
                    $query->setFrom(
                        (int)$options[SelectorSourceInterface::OFFSET]
                    );
                }

                $orders = $options[SelectorSourceInterface::ORDERBY];
                if(false === empty($orders) && is_array($orders)) {
                    foreach($orders as $order) {
                        $query->addSort($order);
                    }
                }
            }

            if(false===empty($options[self::GROUPBY])) {
                throw new \Exception('Not implemented yet');
            }

            if(false===empty($options[self::FACETS])) {
                foreach($options[self::FACETS] as $facet) {
                    $query->addFacet($facet);
                }
            }
            $this->resultSets[$resultSetKey] = $index->search($query);
        }

        //fields
        if (self::RESULT_COUNT === $options[SelectorSourceInterface::RESULT]) {
            return $this->resultSets[$resultSetKey]->getTotalHits();
        } elseif (self::RESULT_IDENTIFIERS === $options[SelectorSourceInterface::RESULT]) {
            $toReturn = array();
            $ids = array();
            foreach ($options[SelectorSourceInterface::ID_FIELD] as $idField) {
                $idField = explode(' as ',$idField);
                $ids[$idField[0]] = isset($idField[1]) ? $idField[1] : $idField[0];
            }
            if(empty($ids)) {
                throw new \RuntimeException('No id to get back');
            }

            foreach($this->resultSets[$resultSetKey]->getResults() as $result) {
                $row = array();
                $data = $result->getData();
                foreach ($ids as $idOriginal => $idAlias) {
                    $row[$idAlias] = $data[$idOriginal];//Doesn't handle id placed in a nested array
                }
                $toReturn[] = $row;
            }

            return $toReturn;
        } else {
            throw new \Exception('Unknown result type');
        }
    }

    public function saveIds($ids, array $criterias, array $options=array()){
        throw new \Exception("ElasticSearch selector source can't save result");
    }

    public function invalidateGlobal(array $options = array()){

    }

    public function getResultSet($query, $limit, $offset, $orderBy, $facets) {
        return $this->resultSets[$this->getResultSetKey($query, $limit, $offset, $orderBy, $facets)];
    }
}
