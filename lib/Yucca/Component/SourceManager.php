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

use Yucca\Component\Source\Chain;
use Yucca\Component\SourceFactory\SourceFactoryInterface;

class SourceManager
{
    /**
     * @var array
     */
    protected $sourceConfig;

    /**
     * @var SourceFactoryInterface[]
     */
    protected $sourceFactories;

    /**
     * @var \Yucca\Component\SchemaManager
     */
    protected $schemaManager;

    /**
     * @var \Yucca\Component\Source\SourceInterface[]
     */
    protected $sources;

    /**
     * @param array $sourceConfig
     */
    public function __construct(array $sourceConfig) {
        $this->sourceConfig = $sourceConfig;
    }

    /**
     * Add a source factory to the pool
     * @param string $sourceFactoryName
     * @param \Yucca\Component\SourceFactory\SourceFactoryInterface $sourceFactory
     * @return \Yucca\Component\SourceManager
     */
    public function addSourceFactory($sourceFactoryName, SourceFactoryInterface $sourceFactory) {
        $this->sourceFactories[$sourceFactoryName] = $sourceFactory;
        return $this;
    }

    /**
     * get factory by its type
     * @param $type
     * @return SourceFactory\SourceFactoryInterface
     * @throws \Exception
     */
    protected function getFactory($type){
        if(isset($this->sourceFactories[$type])){
            return $this->sourceFactories[$type];
        } else {
            throw new \Exception("Factory \"$type\" not foud");
        }
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
                $sources[] = $this->getFactory($sourceConfig['type'])->getSource($sourceName, $params);
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
