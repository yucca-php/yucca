<?php
namespace Yucca\Component\Selector\SourceFactory;

interface SelectorSourceFactoryInterface {
    /**
     * build factory
     * @abstract
     * @return \Yucca\Component\Selector\Source\SelectorSourceInterface
     */
    function getSource();
}