<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Concrete\Selector;

use \Yucca\Component\Selector\SelectorAbstract;
use \Yucca\Component\Selector\Source\SelectorSourceInterface;

class Base extends SelectorAbstract {

    public function __construct(SelectorSourceInterface $source = null){
        parent::__construct($source);
        $this->options = array();
    }
}
