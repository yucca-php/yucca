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

use Yucca\Component\SourceManager;

class MappingManager {

    protected $mappersConfiguration = array();

    protected $mappers = array();

    /**
     * @var \Yucca\Component\SourceManager
     */
    protected $sourceManager;

    public function __construct(array $mappersConfiguration){
        $this->mappersConfiguration = $mappersConfiguration;
    }

    public function setSourceManager(SourceManager $sourceManager){
        $this->sourceManager = $sourceManager;
    }

    /**
     * @param $className
     * @return \Yucca\Component\Mapping\Mapper
     * @throws \RuntimeException
     */
    public function getMapper($className) {
        if( false === isset($this->mappersConfiguration[$className])) {
            throw new \RuntimeException("$className can't be handled by the Mapping Manager");
        }

        if( false === isset($this->mappers[$className])){
            $mapperClassName = isset($this->mappersConfiguration[$className]['mapper_class_name'])
                ? $this->mappersConfiguration[$className]['mapper_class_name']
                : 'Yucca\Component\Mapping\Mapper';
            $this->mappers[$className] = new $mapperClassName($className, $this->mappersConfiguration[$className]);
            $this->mappers[$className]->setSourceManager($this->sourceManager);
        }

        return $this->mappers[$className];
    }
}
