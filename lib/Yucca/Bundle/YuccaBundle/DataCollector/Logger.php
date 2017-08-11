<?php
namespace Yucca\Bundle\YuccaBundle\DataCollector;


use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

class Logger
{
    protected $calls = array();

    /**
     *
     */
    public function addCall($handler, $action, $result, $context, $duration)
    {
        $this->calls[$handler][$action][$result][] = array(
            'context'=>$context,
            'duration'=>$duration
        );
    }

    public function getCalls()
    {
        return $this->calls;
    }
}
