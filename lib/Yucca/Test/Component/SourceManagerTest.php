<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yucca\Test\Component;

use PHPUnit\Framework\TestCase;

class SourceManagerTest extends TestCase
{

    public function test_getSource()
    {
        $sourceManager = new \Yucca\Component\SourceManager(array(
            'source1'=>array(
                'default_params'=>array('id'=>array('type'=>'identifier'), 'firstName'=>null),
                'handlers'=>array(
                    array('type'=>'memcache','connection_name'=>'memcache_connection'),
                    array('type'=>'database_single_row','table_name'=>'table1')
                )
            )
        ));

        //Not configured
        try {
            $sourceManager->getSource('fake1');
            $this->fail('Should raise an exception');
        } catch (\Exception $e) {
            $this->assertContains('fake1', $e->getMessage());
        }

        //configured but missing factory
        $databaseSingleRowFactory = $this->getMockBuilder('\Yucca\Component\SourceFactory\DatabaseSingleRowFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $sourceManager->addSourceFactory('database_single_row', $databaseSingleRowFactory);


        try {
            $sourceManager->getSource('source1');
            $this->fail('Should raise an exception');
        } catch (\Exception $e) {
            $this->assertContains('memcache', $e->getMessage());
        }

        //Missing chain factory
        $memcacheFactory = $this->getMockBuilder('\Yucca\Component\SourceFactory\MemcacheFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $sourceManager->addSourceFactory('memcache', $memcacheFactory);

        try {
            $sourceManager->getSource('source1');
            $this->fail('Should raise an exception');
        } catch (\Exception $e) {
            $this->assertContains('chain', $e->getMessage());
        }

        //correct
        $chainFactory = $this->getMockBuilder('\Yucca\Component\SourceFactory\ChainFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $sourceManager->addSourceFactory('chain', $chainFactory);

        $first = $sourceManager->getSource('source1');
        $this->assertSame($first, $sourceManager->getSource('source1'));
    }

    public function test_getSourceSingleHandler()
    {
        $sourceManager = new \Yucca\Component\SourceManager(array(
            'source1'=>array(
                'default_params'=>array('id'=>array('type'=>'identifier'), 'firstName'=>null),
                'handlers'=>array(
                    array('type'=>'database_single_row','table_name'=>'table1')
                )
            )
        ));


        $databaseSingleRowFactory = $this->getMockBuilder('\Yucca\Component\SourceFactory\DatabaseSingleRowFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $sourceManager->addSourceFactory('database_single_row', $databaseSingleRowFactory);

        $first = $sourceManager->getSource('source1');
        $this->assertSame($first, $sourceManager->getSource('source1'));
    }
}
