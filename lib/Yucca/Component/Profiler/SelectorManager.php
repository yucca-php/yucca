<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Component\Profiler;

use Symfony\Component\Stopwatch\Stopwatch;
use Yucca\Bundle\YuccaBundle\DataCollector\Logger;
use Yucca\Component\Selector\Source\LogWrapper;
use Yucca\Component\SelectorManager as BaseSelectorManager;

/**
 * Class SelectorManager
 * @package Yucca\Component\Profiler
 */
class SelectorManager extends BaseSelectorManager
{
    protected $stopWatch;

    protected $datacollectorLogger;

    /**
     * SelectorManager constructor.
     *
     * @param array     $selectorSourceConfig
     * @param Stopwatch $stopWatch
     * @param Logger    $datacollectorLogger
     */
    public function __construct(array $selectorSourceConfig, Stopwatch $stopWatch = null, Logger $datacollectorLogger)
    {
        parent::__construct($selectorSourceConfig);
        $this->stopWatch = $stopWatch;
        $this->datacollectorLogger = $datacollectorLogger;
    }

    /**
     * Get a source by it's name
     * @param $selectorSourceName
     * @return \Yucca\Component\Selector\Source\SelectorSourceInterface
     * @throws \InvalidArgumentException
     */
    protected function getSource($selectorSourceName)
    {
        if (false === isset($this->sources[$selectorSourceName])) {
            $this->sources[$selectorSourceName] = new LogWrapper(
                $this->getFactory($selectorSourceName)->getSource(),
                $this->stopWatch,
                $this->datacollectorLogger
            );
        }

        return $this->sources[$selectorSourceName];
    }
}
