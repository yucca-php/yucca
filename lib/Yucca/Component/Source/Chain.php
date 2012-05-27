<?php
namespace Yucca\Component\Source;

use \Yucca\Component\Source\Exception\NoDataException;
use \Yucca\Component\Source\Exception\BreakSaveChainException;
use \Yucca\Component\Source\Exception\BreakRemoveChainException;
use \Yucca\Component\ConnectionManager;
use Yucca\Component\Source\DataParser\DataParser;

class Chain extends SourceAbstract
{
    /**
     * @var \Yucca\Component\Source\SourceAbstract[]
     */
    protected $sources;

    /**
     * @var \Yucca\Component\Source\DataParser\DataParser
     */
    protected $dataParser;

    public function __construct($sourceName, array $configuration=array(), $sources=array()) {
        parent::__construct($sourceName, $configuration);

        if(empty($sources)){
            throw new \InvalidArgumentException("\"sources\" must be a non empty array");
        }
        $this->sources = $sources;
    }

    /**
     * @param $fieldName
     * @return bool
     */
    public function canHandle($fieldName){
        foreach($this->sources as $source){
            if($source->canHandle($fieldName)){
                return true;
            }
        }

        return false;
    }

    /**
     * @param DataParser\DataParser $dataParser
     * @return DatabaseSingleRow
     */
    public function setDataParser(DataParser $dataParser){
        $this->dataParser = $dataParser;
        return $this;
    }

    /**
     * @param array $identifier
     * @throws Exception\NoDataException
     * @return array
     */
    public function load(array $identifier){
        $sourcesToFeed = array();
        $datas = null;
        foreach($this->sources as $sourceKey=>$source){
            try {
                $datas = $source->load($identifier, true);
                break;
            } catch (NoDataException $exception) {
                $sourcesToFeed[] = $sourceKey;
            }
        }

        if(isset($datas)){
            foreach($sourcesToFeed as $sourceKey){
                $this->sources[$sourceKey]->saveAfterLoading($datas, $identifier);
            }

            return $this->dataParser->decode($datas, $this->configuration['fields']);
        }

        throw new NoDataException("Chain can't load datas for source {$this->sourceName} with ids : ".var_export($identifier,true));
    }

    /**
     * @param array $identifier
     * @return array
     */
    public function remove(array $identifier){
        try {
            foreach($this->sources as $source){
                $source->remove($identifier);
            }
        } catch(BreakRemoveChainException $e) {

        }
    }

    public function saveAfterLoading($datas, array $identifier=array(), &$affectedRows=null){
        throw new \Exception("Don't know what to do in chain {$this->sourceName}...");
    }

    /**
     * Save datas
     * @param $datas
     * @param array $identifier
     * @param array $affectedRows
     * @return int
     */
    public function save($datas, array $identifier=array(), &$affectedRows=null){
        $toReturn = array();
        try {
            foreach($this->sources as $source){
                $justCreated = $source->save($datas, $identifier, $affectedRows);
                if(is_array($justCreated)) {
                    $toReturn = array_merge($toReturn, $justCreated);
                }
            }
        } catch(BreakSaveChainException $e) {

        }
        return $toReturn;
    }
}
