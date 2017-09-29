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

use \Yucca\Component\Selector\Source\Sphinx;
use \Yucca\Component\ConnectionManager;

/**
 * Class SphinxFactory
 * @package Yucca\Component\Selector\SourceFactory
 */
class SphinxFactory implements SelectorSourceFactoryInterface
{
    /**
     * @var \Yucca\Component\ConnectionManager
     */
    protected $connectionManager;

    /**
     * Constructor
     * @param \Yucca\Component\ConnectionManager $connectionManager
     */
    public function __construct(ConnectionManager $connectionManager)
    {
        $this->connectionManager = $connectionManager;
    }

    /**
     * Build source
     * @return \Yucca\Component\Selector\Source\Memcache
     */
    public function getSource()
    {
        $toReturn = new Sphinx();
        $toReturn->setConnectionManager($this->connectionManager);

        return $toReturn;
    }
}
