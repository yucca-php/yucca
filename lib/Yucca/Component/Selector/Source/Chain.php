<?php
namespace Yucca\Component\Selector\Source;

use \Yucca\Component\Selector\Exception\NoDataException;
use \Yucca\Component\Selector\Exception\BreakSaveChainException;

class Chain implements SelectorSourceInterface
{
    /**
     * @var \Yucca\Component\Selector\Source\SelectorSourceInterface[]
     */
    protected $sources;

    public function __construct($sources=array()) {
        if(empty($sources)){
            throw new \InvalidArgumentException("\"sources\" must be a non empty array");
        }
        $this->sources = $sources;
    }

    /**
     * @param array $criterias
     * @param array $options
     * @throws \Yucca\Component\Selector\Exception\NoDataException
     * @return array
     */
    public function loadIds(array $criterias, array $options=array()){
        $sourcesToFeed = array();
        $datas = null;
        foreach($this->sources as $sourceKey=>$source){
            try {
                $datas = $source->loadIds($criterias, $options);
                break;
            } catch (NoDataException $exception) {
                $sourcesToFeed[] = $sourceKey;
            }
        }

        if(isset($datas)){
            foreach($sourcesToFeed as $sourceKey){
                $this->sources[$sourceKey]->saveIds($datas, $criterias, $options);
            }
        }

        if(empty($datas)) {
            throw new NoDataException("Chain can't load datas for selector source");
        }

        return $datas;
    }

    public function saveIds(array $ids, array $criterias, array $options = array()){
        throw new \Exception("Don't know what to do in chain...");
    }

    public function invalidateGlobal(array $options = array()){
        foreach($this->sources as $source){
            $source->invalidateGlobal($options);
        }
    }
}
