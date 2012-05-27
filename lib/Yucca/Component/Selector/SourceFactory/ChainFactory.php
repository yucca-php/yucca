<?php
namespace Yucca\Component\Selector\SourceFactory;

use \Yucca\Component\Selector\Source\Chain;

class ChainFactory implements SelectorSourceFactoryInterface
{
    /**
     * Build source
     * @param array $sources
     * @return \Yucca\Component\Selector\Source\Chain
     */
    public function getSource(array $sources=array()) {
        $toReturn = new Chain($sources);

        return $toReturn;
    }
}
