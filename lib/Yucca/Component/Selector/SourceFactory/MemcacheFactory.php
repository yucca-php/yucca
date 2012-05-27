<?php
namespace Yucca\Component\Selector\SourceFactory;

use \Yucca\Component\Selector\Source\Memcache;
use \Yucca\Component\ConnectionManager;

class MemcacheFactory implements SelectorSourceFactoryInterface
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
        $toReturn = new Memcache();
        $toReturn->setConnectionManager($this->connectionManager);

        return $toReturn;
    }
}
