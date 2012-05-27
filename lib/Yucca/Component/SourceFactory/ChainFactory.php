<?php
namespace Yucca\Component\SourceFactory;

use \Yucca\Component\Source\Chain;
use Yucca\Component\Source\DataParser\DataParser;

class ChainFactory implements SourceFactoryInterface
{
    /**
     * @var \Yucca\Component\Source\DataParser\DataParser
     */
    protected $dataParser;

    /**
     * @param \Yucca\Component\Source\DataParser\DataParser $dataParser
     */
    public function __construct(DataParser $dataParser) {
        $this->dataParser = $dataParser;
    }
    /**
     * Build source
     * @param $sourceName
     * @param array $params
     * @param array $sources
     * @return \Yucca\Component\Source\Chain
     */
    public function getSource($sourceName, array $params = array(), array $sources=array()) {
        $toReturn = new Chain($sourceName, $params, $sources);
        $toReturn->setDataParser($this->dataParser);

        return $toReturn;
    }
}
