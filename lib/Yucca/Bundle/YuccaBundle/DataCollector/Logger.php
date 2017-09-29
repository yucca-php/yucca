<?php
namespace Yucca\Bundle\YuccaBundle\DataCollector;

/**
 * Class Logger
 * @package Yucca\Bundle\YuccaBundle\DataCollector
 */
class Logger
{
    protected $calls = array();

    /**
     * @param mixed $handler
     * @param mixed $action
     * @param mixed $result
     * @param mixed $context
     * @param mixed $duration
     */
    public function addCall($handler, $action, $result, $context, $duration)
    {
        $this->calls[$handler][$action][$result][] = array(
            'context'=>$context,
            'duration'=>$duration,
        );
    }

    /**
     * @return array
     */
    public function getCalls()
    {
        return $this->calls;
    }
}
