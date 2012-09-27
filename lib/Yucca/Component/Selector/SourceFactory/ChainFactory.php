<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
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
