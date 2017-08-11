<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Component\SourceFactory;

use \Yucca\Component\Source\Memcached;
use \Yucca\Component\ConnectionManager;
use \Yucca\Component\Source\DataParser\DataParser;
use Yucca\Component\Source\PhpArray;

class PhpArrayFactory implements SourceFactoryInterface
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
     * @return \Yucca\Component\Source\Memcache
     */
    public function getSource($sourceName, array $params = array()) {
        $toReturn = new PhpArray($sourceName, $params);
        $toReturn->setDataParser($this->dataParser);

        return $toReturn;
    }
}
