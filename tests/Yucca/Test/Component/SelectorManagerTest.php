<?php

namespace Yucca\Test\Component;

class SelectorManagerTest extends \PHPUnit_Framework_TestCase {

    public function test_getSelector(){
        $selectorManager = new \Yucca\Component\SelectorManager(array(
            'Yucca\Concrete\Selector\Base'=>array(
                'sources'=>array(
                    'database',
                    'memcache',
                )
            )
        ));

        //Not configured
        try {
            $selectorManager->getSelector('Yucca\Concrete\Selector\Fake');
            $this->fail('Should raise an exception');
        } catch (\Exception $e){
            $this->assertContains('Yucca\Concrete\Selector\Fake', $e->getMessage());
        }

        //configured but missing factory
        $databaseFactory = $this->getMockBuilder('\Yucca\Component\Selector\SourceFactory\DatabaseFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $selectorManager->addSelectorSourceFactory('database',$databaseFactory);


        try {
            $selectorManager->getSelector('Yucca\Concrete\Selector\Base');
            $this->fail('Should raise an exception');
        } catch (\Exception $e){
            $this->assertContains('memcache', $e->getMessage());
        }

        //Missing chain factory
        $memcacheFactory = $this->getMockBuilder('\Yucca\Component\Selector\SourceFactory\MemcacheFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $selectorManager->addSelectorSourceFactory('memcache',$memcacheFactory);

        try {
            $selectorManager->getSelector('Yucca\Concrete\Selector\Base');
            $this->fail('Should raise an exception');
        } catch (\Exception $e){
            $this->assertContains('chain', $e->getMessage());
        }

        //correct
        $chainFactory = $this->getMockBuilder('\Yucca\Component\Selector\SourceFactory\ChainFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $selectorManager->addSelectorSourceFactory('chain',$chainFactory);

        $this->assertInstanceOf('Yucca\Component\Selector\SelectorAbstract',$selectorManager->getSelector('Yucca\Concrete\Selector\Base'));
    }

    public function test_getSourceSingleHandler(){
        $selectorManager = new \Yucca\Component\SelectorManager(array(
            'Yucca\Concrete\Selector\Base'=>array(
                'sources'=>array(
                    'database',
                )
            )
        ));

        $databaseFactory = $this->getMockBuilder('\Yucca\Component\Selector\SourceFactory\DatabaseFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $selectorManager->addSelectorSourceFactory('database',$databaseFactory);

        $this->assertInstanceOf('Yucca\Component\Selector\SelectorAbstract',$selectorManager->getSelector('Yucca\Concrete\Selector\Base'));
    }

}