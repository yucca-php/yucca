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

use \Yucca\Component\Selector\Source\Memcached;
use \Yucca\Component\ConnectionManager;

class MemcachedFactory implements SelectorSourceFactoryInterface
{
    /**
     * @var \Yucca\Component\ConnectionManager
     */
    protected $connectionManager;

    /**
     * Constructor
     * @param \Yucca\Component\ConnectionManager $connectionManager
     */
    public function __construct(ConnectionManager $connectionManager) {
        $this->connectionManager = $connectionManager;
    }

    /**
     * Build source
     * @return \Yucca\Component\Selector\Source\Memcache
     */
    public function getSource() {
        $toReturn = new Memcached();
        $toReturn->setConnectionManager($this->connectionManager);

        return $toReturn;
    }
}
