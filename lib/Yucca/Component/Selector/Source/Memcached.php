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

use Yucca\Component\ConnectionManager;
use Yucca\Component\Selector\Exception\NoDataException;

/**
 * Class Memcached
 * @package Yucca\Component\Selector\Source
 */
class Memcached implements SelectorSourceInterface
{

    /**
     * @var \Yucca\Component\ConnectionManager
     */
    protected $connectionManager;

    /**
     * @param ConnectionManager $connectionManager
     */
    public function setConnectionManager(ConnectionManager $connectionManager)
    {
        $this->connectionManager = $connectionManager;
    }

    /**
     * @param array $criterias
     * @param array $options
     * @return string
     * @throws \Exception
     */
    public function loadIds(array $criterias, array $options = array())
    {
        $options = $this->mergeOptions($options);

        $connection = $this->connectionManager->getConnection($options[SelectorSourceInterface::CONNECTION_NAME]);
        $result = $connection->get($this->getCacheKey($criterias, $options));

        if (false === $result) {
            throw new NoDataException('No data');
        } else {
            return $result;
        }
    }

    /**
     * @param array $ids
     * @param array $criterias
     * @param array $options
     * @return mixed
     */
    public function saveIds($ids, array $criterias, array $options = array())
    {
        $options = $this->mergeOptions($options);

        /**
         * @var $connection \Memcache
         */
        $connection = $this->connectionManager->getConnection($options[SelectorSourceInterface::CONNECTION_NAME]);

        return $connection->set($this->getCacheKey($criterias, $options), $ids, 0);
    }

    /**
     * @param array $options
     */
    public function invalidateGlobal(array $options = array())
    {
        /**
         * @var $connection \Memcache
         */
        $connection = $this->connectionManager->getConnection($options[SelectorSourceInterface::CONNECTION_NAME]);
        $connection->delete($this->getCacheKey(array(), array_merge($options, array(SelectorSourceInterface::RESULT => SelectorSourceInterface::RESULT_IDENTIFIERS))));
        $connection->delete($this->getCacheKey(array(), array_merge($options, array(SelectorSourceInterface::RESULT => SelectorSourceInterface::RESULT_COUNT))));
    }

    protected function getCacheKey(array $criterias, array $options = array())
    {
        //fields
        if (SelectorSourceInterface::RESULT_COUNT === $options[SelectorSourceInterface::RESULT]) {
            $suffix = 'count';
        } elseif (SelectorSourceInterface::RESULT_IDENTIFIERS === $options[SelectorSourceInterface::RESULT]) {
            $suffix = 'content';
        } else {
            throw new \Exception('Unknown result type');
        }

        $cacheKey = array();
        foreach ($criterias as $criteriaKey => $criteriaValue) {
            if (false === is_array($criteriaValue)) {
                $criteriaValue = array($criteriaValue);
            }

            foreach ($criteriaValue as $v) {
                if ($v instanceof \Yucca\Model\ModelInterface) {
                    $cacheKey[] = $criteriaKey.'-'.$v->getId().'-'.($v->getUpdatedAt() instanceof \DateTime ? $v->getUpdatedAt()->format('c') : $v->getUpdatedAt());
                } elseif (is_scalar($v) || is_null($v)) {
                    $cacheKey[] = $criteriaKey.'-'.var_export($v, true);
                } elseif ($v instanceof \Yucca\Component\Selector\Expression) {
                    $expression = $v->toString('memcache');
                    if (false === empty($expression)) {
                        $cacheKey[] = $expression;
                    }
                } else {
                    throw new \Exception("Can't use criteria $criteriaKey");
                }
            }
        }

        return $options[SelectorSourceInterface::SELECTOR_NAME].':'.md5(var_export($options, true)).':'.md5(implode(':', $cacheKey)).':'.$suffix;
    }

    protected function mergeOptions(array $options)
    {
        //Merge options
        $defaultOptions = array(
            SelectorSourceInterface::RESULT => SelectorSourceInterface::RESULT_IDENTIFIERS,
            SelectorSourceInterface::CONNECTION_NAME => '',
            SelectorSourceInterface::SELECTOR_NAME => '',
        );

        $options = array_merge($defaultOptions, $options);

        if (empty($options[SelectorSourceInterface::CONNECTION_NAME])) {
            throw new \Exception('A connection name must be given to the selector source');
        }

        if (empty($options[SelectorSourceInterface::SELECTOR_NAME])) {
            throw new \Exception('A selector name must be given to the selector source');
        }

        return $options;
    }
}
