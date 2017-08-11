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
use Yucca\Component\Source\LogWrapper;
use Yucca\Component\SourceManager as BaseSourceManager;

class SourceManager extends BaseSourceManager
{

    protected $stopWatch;

    protected $datacollectorLogger;

    /**
     * SourceManager constructor.
     * @param array $sourceConfig
     * @param Stopwatch|null $stopWatch
     */
    public function __construct(array $sourceConfig, Stopwatch $stopWatch=null, Logger $datacollectorLogger) {
        parent::__construct($sourceConfig);
        $this->stopWatch = $stopWatch;
        $this->datacollectorLogger = $datacollectorLogger;
    }

    /**
     * Get a source by it's name
     * @param $sourceName
     * @return \Yucca\Component\Source\SourceInterface
     * @throws \InvalidArgumentException
     */
    public function getSource($sourceName){
        if(false === isset($this->sources[$sourceName])){
            if(false === isset($this->sourceConfig[$sourceName]['handlers'])){
                throw new \InvalidArgumentException("\"$sourceName\" name has not been configured");
            }

            $sources = array();
            foreach($this->sourceConfig[$sourceName]['handlers'] as $sourceConfig){
                $params = array_merge($this->sourceConfig[$sourceName]['default_params'], $sourceConfig);
                $sources[] = new LogWrapper(
                    $this->getFactory($sourceConfig['type'])->getSource($sourceName, $params),
                    $this->stopWatch,
                    $this->datacollectorLogger,
                    $sourceName
                );
            }

            if(1 === count($sources)){
                $this->sources[$sourceName] = current($sources);
            } else {
                $this->sources[$sourceName] = $this->getFactory('chain')->getSource($sourceName, $this->sourceConfig[$sourceName]['default_params'], $sources);
            }
        }

        return $this->sources[$sourceName];
    }
}
