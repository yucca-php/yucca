<?php
namespace Yucca\Concrete\Selector;

use \Yucca\Component\Selector\SelectorAbstract;
use \Yucca\Component\Selector\Source\SelectorSourceInterface;

class Base extends SelectorAbstract {

    public function __construct(SelectorSourceInterface $source = null){
        parent::__construct($source);
        $this->options = array();
    }
}