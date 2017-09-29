<?php
namespace Yucca\Component\Selector\Source;

use Symfony\Component\Stopwatch\Stopwatch;
use Yucca\Bundle\YuccaBundle\DataCollector\Logger;

/**
 * Class LogWrapper
 * @package Yucca\Component\Selector\Source
 */
class LogWrapper implements SelectorSourceInterface
{
    /**
     * @var \Yucca\Component\Selector\Source\SelectorSourceInterface
     */
    protected $source;

    protected $sourceHandlerName;

    protected $stopWatch;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * LogWrapper constructor.
     * @param SelectorSourceInterface $source
     * @param Stopwatch               $stopWatch
     * @param Logger                  $logger
     */
    public function __construct(SelectorSourceInterface $source, Stopwatch $stopWatch, Logger $logger)
    {
        $this->source = $source;
        $this->sourceHandlerName = (new \ReflectionClass($source))->getShortName();
        ;
        $this->stopWatch = $stopWatch;
        $this->logger = $logger;
    }

    /**
     * @param array $criterias
     * @param array $options
     * @return mixed
     */
    public function loadIds(array $criterias, array $options = array())
    {
        $eventName = 'loadIds.'.$this->sourceHandlerName;
        $this->stopWatch->start($eventName, 'propel');

        try {
            $toReturn = $this->source->loadIds($criterias, $options);

            $event = $this->stopWatch->stop($eventName);
            $periodsCount = count($event->getPeriods());
            $this->logger->addCall(
                $this->sourceHandlerName,
                "loadIds",
                'hit',
                array(
                    "criterias" => $criterias,
                    "options" => $this->filterOptions($options),
                ),
                $event->getPeriods()[$periodsCount - 1]->getDuration()
            );

            return $toReturn;
        } catch (\Exception $e) {
            $event = $this->stopWatch->stop($eventName);
            $periodsCount = count($event->getPeriods());
            $this->logger->addCall(
                $this->sourceHandlerName,
                "loadIds",
                'miss',
                array(
                    "exception"=>$e->getMessage(),
                    "criterias" => $criterias,
                    "options" => $this->filterOptions($options),
                ),
                $event->getPeriods()[$periodsCount - 1]->getDuration()
            );

            throw $e;
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
        $eventName = 'saveIds.'.$this->sourceHandlerName;
        $this->stopWatch->start($eventName, 'propel');

        try {
            $toReturn = $this->source->saveIds($ids, $criterias, $options);

            $event = $this->stopWatch->stop($eventName);
            $periodsCount = count($event->getPeriods());
            $this->logger->addCall(
                $this->sourceHandlerName,
                "saveIds",
                'hit',
                array(
                    "ids"=>$ids,
                    "criterias"=>$criterias,
                    "options"=>$this->filterOptions($options),
                ),
                $event->getPeriods()[$periodsCount - 1]->getDuration()
            );

            return $toReturn;
        } catch (\Exception $e) {
            $event = $this->stopWatch->stop($eventName);
            $periodsCount = count($event->getPeriods());
            $this->logger->addCall(
                $this->sourceHandlerName,
                "saveIds",
                'miss',
                array(
                    "ids"=>$ids,
                    "criterias"=>$criterias,
                    "options"=>$this->filterOptions($options),
                ),
                $event->getPeriods()[$periodsCount - 1]->getDuration()
            );

            throw $e;
        }
    }

    /**
     * @param array $options
     * @return mixed
     */
    public function invalidateGlobal(array $options = array())
    {
        $eventName = 'invalidateGlobal.'.$this->sourceHandlerName;
        $this->stopWatch->start($eventName, 'propel');

        try {
            $toReturn = $this->source->invalidateGlobal($options);

            $event = $this->stopWatch->stop($eventName);
            $periodsCount = count($event->getPeriods());
            $this->logger->addCall(
                $this->sourceHandlerName,
                "invalidateGlobal",
                'hit',
                array(
                    "options"=>$this->filterOptions($options),
                ),
                $event->getPeriods()[$periodsCount - 1]->getDuration()
            );

            return $toReturn;
        } catch (\Exception $e) {
            $event = $this->stopWatch->stop($eventName);
            $periodsCount = count($event->getPeriods());
            $this->logger->addCall(
                $this->sourceHandlerName,
                "invalidateGlobal",
                'miss',
                array(
                    "options"=>$this->filterOptions($options),
                ),
                $event->getPeriods()[$periodsCount - 1]->getDuration()
            );

            throw $e;
        }
    }

    /**
     * @param $options
     * @return array
     */
    protected function filterOptions($options)
    {
        $toReturn=array();
        foreach ($options as $k => $v) {
            try {
                serialize($v);
                $toReturn[$k] = $v;
            } catch (\Throwable $error) {
                //Nothing to do
            }
        }

        return $toReturn;
    }
}
