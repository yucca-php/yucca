<?php
namespace Yucca\Component\SourceFactory;

interface SourceFactoryInterface {
    /**
     * build factory
     * @abstract
     * @param $sourceName
     * @param array $params
     * @return mixed
     */
    function getSource($sourceName, array $params=array());
}