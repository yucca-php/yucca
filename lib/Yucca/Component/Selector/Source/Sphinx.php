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

use Yucca\Component\SchemaManager;
use Yucca\Component\Selector\Exception\NoDataException;
use Yucca\Component\ConnectionManager;

class Sphinx implements SelectorSourceInterface{

    /**
     * @var \Yucca\Component\ConnectionManager
     */
    protected $connectionManager;

    public function setConnectionManager(ConnectionManager $connectionManager){
        $this->connectionManager = $connectionManager;
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
            SelectorSourceInterface::RESULT => SelectorSourceInterface::RESULT_IDENTIFIERS,
        );
        $options = array_merge($defaultOptions, $options);

        //Check options
        if(empty($options[SelectorSourceInterface::TABLE])){
            throw new \Exception('Table must be set for selector source');
        }
        if(empty($options[SelectorSourceInterface::ID_FIELD])){
            throw new \Exception('Id Field must be set for selector source');
        }

        /**
         * @var $sphinx \SphinxClient
         */
        $sphinx = $this->connectionManager->getConnection($options[SelectorSourceInterface::CONNECTION_NAME]);
        $sphinx->ResetFilters();

        foreach ($criterias as $criteriaName => $criteria) {
            if('text' == $criteriaName) {
                continue;
            }
            $encodedCriteria = array();
            if (false === is_array($criteria)) {
                $criteria = array($criteria);
            }
            foreach($criteria as $criteriaValue) {
                if($criteriaValue instanceof \Yucca\Model\ModelInterface) {
                    $encodedCriteria[] = $criteriaValue->getId();
                } elseif(is_scalar($criteriaValue)) {
                    $encodedCriteria[] = $criteriaValue;
                } else {
                    throw new \Exception("Can't use criteria $criteriaName");
                }
            }
            $sphinx->SetFilter($criteriaName, $encodedCriteria);
        }

        if (self::RESULT_COUNT !== $options[SelectorSourceInterface::RESULT]) {
            if(!is_numeric($options[SelectorSourceInterface::OFFSET])) {
                throw new \RuntimeException('Offset must be a numeric');
            }
            if(!is_numeric($options[SelectorSourceInterface::LIMIT])) {
                throw new \RuntimeException('Limit must be a numeric');
            }
            $sphinx->SetLimits(
                (int)$options[SelectorSourceInterface::OFFSET],
                (int)$options[SelectorSourceInterface::LIMIT]
            );

            $orders = $options[SelectorSourceInterface::ORDERBY];
            if(false === empty($orders) && is_array($orders) && 2 == count($orders)) {
                $sphinx->SetSortMode($orders[0], $orders[1]);
            }
        }

        if(false===empty($options[self::GROUPBY])) {
            $sphinx->SetGroupBy($options[self::GROUPBY]['attribute'], $options[self::GROUPBY]['func'], $options[self::GROUPBY]['groupsort']);
        }

        $sphinx->AddQuery($criterias['text'], $options[SelectorSourceInterface::TABLE]);
        $rawResults = $sphinx->RunQueries();

        if(false === $rawResults) {
            if($sphinx->IsConnectError()) {
                throw new \Yucca\Component\Selector\Exception\SphinxConnectionException($sphinx->GetLastError());
            } else {
                throw new \Yucca\Component\Selector\Exception\SphinxException($sphinx->GetLastError());
            }
        }

        //fields
        if (self::RESULT_COUNT === $options[SelectorSourceInterface::RESULT]) {
            return max($rawResults[0]['total'], $rawResults[0]['total_found']);
        } elseif (self::RESULT_IDENTIFIERS === $options[SelectorSourceInterface::RESULT]) {
            $toReturn = array();
            if(isset($rawResults[0]['matches'])) {
                $matches = $rawResults[0]['matches'];
                $keys = array_keys($matches);
                $size = sizeOf($keys);

                $ids = array();
                foreach ($options[SelectorSourceInterface::ID_FIELD] as $idField) {
                    $idField = explode(' as ',$idField);
                    $ids[$idField[0]] = isset($idField[1]) ? $idField[1] : $idField[0];
                }

                for ($i=0; $i<$size; $i++) {
                    $row = array();
                    foreach ($ids as $idOriginal => $idAlias) {
                        $row[$idAlias] = $matches[$keys[$i]]['attrs'][$idOriginal];
                    }
                    $toReturn[] = $row;
                }
            }

            return $toReturn;
        } else {
            throw new \Exception('Unknown result type');
        }
    }

    public function saveIds(array $ids, array $criterias, array $options=array()){
        throw new \Exception("Database selector source can't save result");
    }

    public function invalidateGlobal(array $options = array()){

    }
}
