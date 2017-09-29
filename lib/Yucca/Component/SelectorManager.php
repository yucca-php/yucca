<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Component;

use Yucca\Component\Selector\SourceFactory\SelectorSourceFactoryInterface;

/**
 * Class SelectorManager
 * @package Yucca\Component
 */
class SelectorManager
{
    /**
     * @var array
     */
    protected $selectorSourceConfig;

    /**
     * @var SelectorSourceFactoryInterface[]
     */
    protected $selectorSourceFactories;

    /**
     * @var \Yucca\Component\Selector\Source\SelectorSourceInterface[]
     */
    protected $sources;

    /**
     * @param array $selectorSourceConfig
     */
    public function __construct(array $selectorSourceConfig)
    {
        $this->selectorSourceConfig = $selectorSourceConfig;
    }

    /**
     * Add a source factory to the pool
     * @param string                                                                 $selectorSourceFactoryName
     * @param \Yucca\Component\Selector\SourceFactory\SelectorSourceFactoryInterface $selectorSourceFactory
     * @return \Yucca\Component\SelectorManager
     */
    public function addSelectorSourceFactory($selectorSourceFactoryName, SelectorSourceFactoryInterface $selectorSourceFactory)
    {
        $this->selectorSourceFactories[$selectorSourceFactoryName] = $selectorSourceFactory;

        return $this;
    }

    /**
     * @param string $selectorClassName
     * @return \Yucca\Component\Selector\SelectorAbstract
     * @throws \Exception
     */
    public function getSelector($selectorClassName)
    {
        if (false === isset($this->selectorSourceConfig[$selectorClassName]['sources'])) {
            throw new \Exception("Selector $selectorClassName is not configured");
        }

        $selectorSources = array();
        foreach ($this->selectorSourceConfig[$selectorClassName]['sources'] as $selectorSourceName) {
            $selectorSources[] = $this->getSource($selectorSourceName);
        }

        if (1 === count($selectorSources)) {
            return new $selectorClassName(current($selectorSources));
        } else {
            return new $selectorClassName($this->getFactory('chain')->getSource($selectorSources));
        }
    }

    /**
     * get factory by its type
     * @param $type
     * @return Selector\SourceFactory\SelectorSourceFactoryInterface
     * @throws \Exception
     */
    protected function getFactory($type)
    {
        if (isset($this->selectorSourceFactories[$type])) {
            return $this->selectorSourceFactories[$type];
        } else {
            throw new \Exception("Factory \"$type\" not foud");
        }
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
            $this->sources[$selectorSourceName] = $this->getFactory($selectorSourceName)->getSource();
        }

        return $this->sources[$selectorSourceName];
    }
}
