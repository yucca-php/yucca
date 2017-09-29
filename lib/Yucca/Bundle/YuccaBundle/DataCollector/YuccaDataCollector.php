<?php
namespace Yucca\Bundle\YuccaBundle\DataCollector;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * Class YuccaDataCollector
 * @package Yucca\Bundle\YuccaBundle\DataCollector
 */
class YuccaDataCollector extends DataCollector
{
    private $logger;

    /**
     * YuccaDataCollector constructor.
     * @param Logger|null $logger
     */
    public function __construct(Logger $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * @param Request         $request
     * @param Response        $response
     * @param \Exception|null $exception
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        if ($this->logger) {
            $this->data = array('yucca' => $this->logger->getCalls());
        } else {
            $this->data =  array('yucca' => array());
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'yucca';
    }

    /**
     * @return int
     */
    public function getCallsCount()
    {
        $toReturn = 0;
        foreach ($this->data['yucca'] as $handler => $actions) {
            foreach ($actions as $actionName => $results) {
                foreach ($results as $result => $events) {
                    $toReturn += count($events);
                }
            }
        }

        return $toReturn;
    }

    /**
     * @param null $handler
     * @param null $action
     * @return int
     */
    public function getTotalDuration($handler = null, $action = null)
    {

        $duration = 0;
        foreach ($this->data['yucca'] as $handlerName => $actions) {
            if (!isset($handler) || $handler == $handlerName) {
                foreach ($actions as $actionName => $results) {
                    if (!isset($action) || $action == $actionName) {
                        foreach ($results as $result => $events) {
                            foreach ($events as $event) {
                                $duration += $event['duration'];
                            }
                        }
                    }
                }
            }
        }

        return $duration;
    }

    /**
     * @return array
     */
    public function getCalls()
    {
        return $this->data['yucca'];
    }
}
