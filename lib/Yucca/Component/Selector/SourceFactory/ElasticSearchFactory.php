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

use Yucca\Component\Selector\Source\ElasticSearch;

class ElasticSearchFactory implements SelectorSourceFactoryInterface
{
    /**
     * Build source
     * @return \Yucca\Component\Selector\Source\ElasticSearch
     */
    public function getSource() {
        $toReturn = new ElasticSearch();

        return $toReturn;
    }
}
