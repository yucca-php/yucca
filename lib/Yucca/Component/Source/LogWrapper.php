<?php
namespace Yucca\Component\Source;

use Symfony\Component\Stopwatch\Stopwatch;
use Yucca\Bundle\YuccaBundle\DataCollector\Logger;

/**
 * Class LogWrapper
 * @package Yucca\Component\Source
 */
class LogWrapper implements SourceInterface
{
    protected $source;

    protected $sourceHandlerName;

    protected $stopWatch;

    /**
     * @var Logger
     */
    protected $logger;

    protected $sourceName;

    /**
     * LogWrapper constructor.
     * @param SourceInterface $source
     * @param Stopwatch       $stopWatch
     * @param Logger          $logger
     * @param string          $sourceName
     */
    public function __construct(SourceInterface $source, Stopwatch $stopWatch, Logger $logger, $sourceName)
    {
        $this->source = $source;
        $this->sourceHandlerName = (new \ReflectionClass($source))->getShortName();
        ;
        $this->stopWatch = $stopWatch;
        $this->logger = $logger;
        $this->sourceName = $sourceName;
    }

    /**
     * @param string $field
     * @return SourceInterface
     */
    public function canHandle($field)
    {
        return $this->source->canHandle($field);
    }

    /**
     * @param string $field
     * @return mixed
     */
    public function isIdentifier($field)
    {
        return $this->source->isIdentifier($field);
    }

    /**
     * @param array $identifier
     * @param mixed $rawData
     * @param mixed $shardingKey
     * @return mixed
     * @throws \Exception
     */
    public function load(array $identifier, $rawData, $shardingKey)
    {
        $eventName = 'load.'.$this->sourceHandlerName;
        $this->stopWatch->start($eventName, 'propel');

        try {
            $toReturn = $this->source->load($identifier, $rawData, $shardingKey);

            $event = $this->stopWatch->stop($eventName);
            $periodsCount = count($event->getPeriods());
            $this->logger->addCall(
                $this->sourceHandlerName,
                "load",
                'hit',
                array(
                    "source"=>$this->sourceName,
                    "rawData"=>$rawData,
                    "identifier"=>$identifier,
                    "shardingKey"=>$shardingKey,
                ),
                $event->getPeriods()[$periodsCount - 1]->getDuration()
            );

            return $toReturn;
        } catch (\Exception $e) {
            $event = $this->stopWatch->stop($eventName);
            $periodsCount = count($event->getPeriods());
            $this->logger->addCall(
                $this->sourceHandlerName,
                "load",
                'miss',
                array(
                    "source"=>$this->sourceName,
                    "rawData"=>$rawData,
                    "identifier"=>$identifier,
                    "shardingKey"=>$shardingKey,
                ),
                $event->getPeriods()[$periodsCount - 1]->getDuration()
            );

            throw $e;
        }
    }

    /**
     * @param mixed $datas
     * @param array $identifier
     * @param null  $shardingKey
     * @param null  $affectedRows
     * @return mixed
     * @throws \Exception
     */
    public function save($datas, array $identifier = array(), $shardingKey = null, &$affectedRows = null)
    {
        $eventName = 'save.'.$this->sourceHandlerName;
        $this->stopWatch->start($eventName, 'propel');

        try {
            $toReturn = $this->source->save($datas, $identifier, $shardingKey, $affectedRows);

            $event = $this->stopWatch->stop($eventName);
            $periodsCount = count($event->getPeriods());
            $this->logger->addCall(
                $this->sourceHandlerName,
                "save",
                'hit',
                array(
                    "source"=>$this->sourceName,
                    "datas"=>$datas,
                    "identifier"=>$identifier,
                    "shardingKey"=>$shardingKey,
                    "affectedRows"=>$affectedRows,
                ),
                $event->getPeriods()[$periodsCount - 1]->getDuration()
            );

            return $toReturn;
        } catch (\Exception $e) {
            $event = $this->stopWatch->stop($eventName);
            $periodsCount = count($event->getPeriods());
            $this->logger->addCall(
                $this->sourceHandlerName,
                "save",
                'miss',
                array(
                    "source"=>$this->sourceName,
                    "datas"=>$datas,
                    "identifier"=>$identifier,
                    "shardingKey"=>$shardingKey,
                    "affectedRows"=>$affectedRows,
                ),
                $event->getPeriods()[$periodsCount - 1]->getDuration()
            );

            throw $e;
        }
    }

    /**
     * @param mixed $datas
     * @param array $identifier
     * @param null  $shardingKey
     * @param null  $affectedRows
     * @return mixed
     * @throws \Exception
     */
    public function saveAfterLoading($datas, array $identifier = array(), $shardingKey = null, &$affectedRows = null)
    {
        $eventName = 'saveAfterLoading.'.$this->sourceHandlerName;
        $this->stopWatch->start($eventName, 'propel');

        try {
            $toReturn = $this->source->saveAfterLoading($datas, $identifier, $shardingKey, $affectedRows);

            $event = $this->stopWatch->stop($eventName);
            $periodsCount = count($event->getPeriods());
            $this->logger->addCall(
                $this->sourceHandlerName,
                "saveAfterLoading",
                'hit',
                array(
                    "source"=>$this->sourceName,
                    "datas" => $datas,
                    "identifier" => $identifier,
                    "shardingKey" => $shardingKey,
                    "affectedRows" => $affectedRows,
                ),
                $event->getPeriods()[$periodsCount - 1]->getDuration()
            );

            return $toReturn;
        } catch (\Exception $e) {
            $event = $this->stopWatch->stop($eventName);
            $periodsCount = count($event->getPeriods());
            $this->logger->addCall(
                $this->sourceHandlerName,
                "saveAfterLoading",
                'miss',
                array(
                    "source"=>$this->sourceName,
                    "datas" => $datas,
                    "identifier" => $identifier,
                    "shardingKey" => $shardingKey,
                    "affectedRows" => $affectedRows,
                ),
                $event->getPeriods()[$periodsCount - 1]->getDuration()
            );

            throw $e;
        }
    }

    /**
     * @param array $identifier
     * @param mixed $shardingKey
     * @return mixed
     * @throws \Exception
     */
    public function remove(array $identifier, $shardingKey)
    {
        $eventName = 'remove.'.$this->sourceHandlerName;
        $this->stopWatch->start($eventName, 'propel');

        try {
            $toReturn = $this->source->remove($identifier, $shardingKey);

            $event = $this->stopWatch->stop($eventName);
            $periodsCount = count($event->getPeriods());
            $this->logger->addCall(
                $this->sourceHandlerName,
                "remove",
                'hit',
                array(
                    "source"=>$this->sourceName,
                    "identifier" => $identifier,
                    "shardingKey" => $shardingKey,
                ),
                $event->getPeriods()[$periodsCount - 1]->getDuration()
            );

            return $toReturn;
        } catch (\Exception $e) {
            $event = $this->stopWatch->stop($eventName);
            $periodsCount = count($event->getPeriods());
            $this->logger->addCall(
                $this->sourceHandlerName,
                "remove",
                'miss',
                array(
                    "source"=>$this->sourceName,
                    "identifier"=>$identifier,
                    "shardingKey"=>$shardingKey,
                ),
                $event->getPeriods()[$periodsCount - 1]->getDuration()
            );

            throw $e;
        }
    }
}
