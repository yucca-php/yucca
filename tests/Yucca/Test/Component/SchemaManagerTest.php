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

class SchemaManagerTest extends \PHPUnit_Framework_TestCase {
    public function test_init(){
        $schemaManager = new \Yucca\Component\SchemaManager(array());
        $schemaManager->setConnectionManager(
            $this->getMockBuilder('Yucca\Component\ConnectionManager')
                ->disableOriginalConstructor()
                ->getMock()
        );
        $schemaManager->addShardingStrategy(
            'fake',
            $this->getMock('Yucca\Component\ShardingStrategy\ShardingStrategyInterface')
        );
    }

    public function test_getConnectionName(){

        $schemaManager = new \Yucca\Component\SchemaManager(array(
            'table0' => array(
                'sharding_strategy'=> 'moduloReturn0',
                'shards' => array('default0','default1')
            ),
            'table1' => array(
                'sharding_strategy'=> 'moduloReturn1',
                'shards' => array('default0','default1')
            ),
            'tableWithoutShardingStrategy' => array(
                'shards' => array('default0')
            ),
            'tableWithoutShardingStrategyButTwoShards' => array(
                'sharding_strategy'=> 'unknown',
                'shards' => array('default0','default1')
            ),
            'tableWithUnknownShardingStrategy' => array(
                'sharding_strategy'=> 'unknown',
                'shards' => array('default0','default1')
            ),
            'tableWithoutConnections' => array(
                'sharding_strategy'=> 'unknown',
                'shards' => array()
            ),
            'tableNotConfiguredButTwoShards' => array(
                'shards' => array('default0','default1')
            ),
            'tableWithOutOfRangeShard' => array(
                'sharding_strategy'=> 'moduloReturn1',
                'shards' => array('default0')
            ),
        ));
        $schemaManager->setConnectionManager(
            $this->getMockBuilder('Yucca\Component\ConnectionManager')
                ->disableOriginalConstructor()
                ->getMock()
        );

        $shardingStrategy = $this->getMock('Yucca\Component\ShardingStrategy\ShardingStrategyInterface');
        $shardingStrategy->expects($this->exactly(10))
            ->method('getShardingIdentifier')
            ->will($this->returnValue(0));

        $schemaManager->addShardingStrategy('moduloReturn0',$shardingStrategy);

        $shardingStrategy = $this->getMock('Yucca\Component\ShardingStrategy\ShardingStrategyInterface');
        $shardingStrategy->expects($this->exactly(10))
            ->method('getShardingIdentifier')
            ->will($this->returnValue(1));

        $schemaManager->addShardingStrategy('moduloReturn1',$shardingStrategy);

        for($i=0;$i<10;$i++) {
            $this->assertSame('default0', $schemaManager->getConnectionName('table0', $i));
        }

        for($i=0;$i<10;$i++) {
            $this->assertSame('default1', $schemaManager->getConnectionName('table1', $i));
        }

        //Fake table
        try {
            $schemaManager->getConnectionName('fakeTable', 0);
            $this->fail('Should raise an exception');
        } catch (\Exception $e){
            $this->assertContains('fakeTable', $e->getMessage());
        }

        //unknown sharding strategy
        try {
            $schemaManager->getConnectionName('tableWithUnknownShardingStrategy', 0);
            $this->fail('Should raise an exception');
        } catch (\Exception $e){
            $this->assertContains('tableWithUnknownShardingStrategy', $e->getMessage());
        }

        //no connections
        try {
            $schemaManager->getConnectionName('tableWithoutConnections', 0);
            $this->fail('Should raise an exception');
        } catch (\Exception $e){
            $this->assertContains('tableWithoutConnections', $e->getMessage());
        }

        //unknown sharding strategy, but two shards given
        try {
            $schemaManager->getConnectionName('tableWithoutShardingStrategyButTwoShards', 0);
            $this->fail('Should raise an exception');
        } catch (\Exception $e){
            $this->assertContains('tableWithoutShardingStrategyButTwoShards', $e->getMessage());
        }

        //not set sharding strategy, but two shards given
        try {
            $schemaManager->getConnectionName('tableNotConfiguredButTwoShards', 0);
            $this->fail('Should raise an exception');
        } catch (\Exception $e){
            $this->assertContains('Table tableNotConfiguredButTwoShards is not configured as sharded. 2 connections found for table tableNotConfiguredButTwoShards and sharding key 0', $e->getMessage());
        }

        try {
            $shardingStrategy = $this->getMock('Yucca\Component\ShardingStrategy\ShardingStrategyInterface');
            $shardingStrategy->expects($this->exactly(1))
                ->method('getShardingIdentifier')
                ->will($this->returnValue(1));
            $schemaManager->addShardingStrategy('moduloReturn1',$shardingStrategy);

            $schemaManager->getConnectionName('tableWithOutOfRangeShard', 1);
            $this->fail('Should raise an exception');
        } catch (\Exception $e){
            $this->assertContains('No connections found for table tableWithOutOfRangeShard and shard 1', $e->getMessage());
        }


        //not set sharding strategy
        for($i=0;$i<10;$i++) {
            $this->assertSame('default0', $schemaManager->getConnectionName('tableWithoutShardingStrategy', $i));
        }
    }

    public function test_fetchOne(){
        $result = array('id'=>1,'fakeField1'=>'ff1','fakeField2'=>'ff2');
        $connection = $this->getMockBuilder('\Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();
        $connection->expects($this->once())
            ->method('fetchAll')
            ->with('SELECT * FROM `table0` WHERE `id`=:id',array(':id'=>1))
            ->will($this->returnValue($result));
        $connectionManager = $this->getMockBuilder('Yucca\Component\ConnectionManager')
            ->disableOriginalConstructor()
            ->getMock();
        $connectionManager->expects($this->once())
            ->method('getConnection')
            ->with('default0')
            ->will($this->returnValue($connection));

        $schemaManager = new \Yucca\Component\SchemaManager(array(
            'table0' => array(
                'sharding_strategy'=> 'moduloReturn0',
                'shards' => array('default0')
            ),
        ));
        $schemaManager->setConnectionManager($connectionManager);

        $shardingStrategy = $this->getMock('Yucca\Component\ShardingStrategy\ShardingStrategyInterface');

        $schemaManager->addShardingStrategy('moduloReturn0',$shardingStrategy);

        try {
            $schemaManager->fetchOne('table0', array());
            $this->fail('Should raise an exception');
        } catch(\Exception $exception) {
            $this->assertSame('Trying to load from table0 with no identifiers',$exception->getMessage());
        }

        $this->assertSame($result , $schemaManager->fetchOne('table0', array('id'=>1)));
    }

    public function test_fetchOneSharded(){
        $result = array('id'=>1,'fakeField1'=>'ff1','fakeField2'=>'ff2');
        $connection = $this->getMockBuilder('\Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();
        $connection->expects($this->once())
            ->method('fetchAll')
            ->with('SELECT * FROM `table1_1` WHERE `id`=:id',array(':id'=>1))
            ->will($this->returnValue($result));
        $connectionManager = $this->getMockBuilder('Yucca\Component\ConnectionManager')
            ->disableOriginalConstructor()
            ->getMock();
        $connectionManager->expects($this->once())
            ->method('getConnection')
            ->with('default1')
            ->will($this->returnValue($connection));

        $schemaManager = new \Yucca\Component\SchemaManager(array(
            'table1' => array(
                'sharding_strategy'=> 'moduloReturn1',
                'shards' => array('default0','default1')
            ),
        ));
        $schemaManager->setConnectionManager($connectionManager);

        $shardingStrategy = $this->getMock('Yucca\Component\ShardingStrategy\ShardingStrategyInterface');
        $shardingStrategy->expects($this->exactly(2))
            ->method('getShardingIdentifier')
            ->will($this->returnValue(1));

        $schemaManager->addShardingStrategy('moduloReturn1',$shardingStrategy);

        $this->assertSame($result , $schemaManager->fetchOne('table1', array('id'=>1,'sharding_key'=>1)));
    }

    public function test_fetchAllOneCriteriaOneValue(){
        $result = array(array('id'=>1),array('id'=>3));
        $connection = $this->getMockBuilder('\Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();
        $connection->expects($this->exactly(2))
            ->method('fetchAll')
            ->with('SELECT id FROM `table0` WHERE `firstName`=:firstName',array(':firstName'=>'Bill'))
            ->will($this->returnValue($result));
        $connectionManager = $this->getMockBuilder('Yucca\Component\ConnectionManager')
            ->disableOriginalConstructor()
            ->getMock();
        $connectionManager->expects($this->exactly(3))
            ->method('getConnection')
            ->with('default0')
            ->will($this->returnValue($connection));

        $schemaManager = new \Yucca\Component\SchemaManager(array(
            'table0' => array(
                'sharding_strategy'=> 'moduloReturn0',
                'shards' => array('default0')
            ),
        ));
        $schemaManager->setConnectionManager($connectionManager);

        try {
            $this->assertSame($result , $schemaManager->fetchIds('table0', array('firstName'=>new \DateTime())));
            $this->fail('Should raise an exception');
        }
        catch (\Exception $exception) {
            $this->assertContains('Don\'t know what to do with criteria firstName', $exception->getMessage());
        }

        $this->assertSame($result , $schemaManager->fetchIds('table0', array('firstName'=>'Bill')));

        $this->assertSame($result , $schemaManager->fetchIds('table0', array('firstName'=>array('Bill'))));
    }

    public function test_fetchAllOneCriteriaMultipleValues(){
        $result = array(array('id'=>1),array('id'=>2));
        $connection = $this->getMockBuilder('\Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();
        $connection->expects($this->exactly(1))
            ->method('fetchAll')
            ->with('SELECT id FROM `table0` WHERE `firstName` IN (:firstName0,:firstName1)',array(':firstName0'=>'Bill',':firstName1'=>'Bob'))
            ->will($this->returnValue($result));
        $connectionManager = $this->getMockBuilder('Yucca\Component\ConnectionManager')
            ->disableOriginalConstructor()
            ->getMock();
        $connectionManager->expects($this->exactly(2))
            ->method('getConnection')
            ->with('default0')
            ->will($this->returnValue($connection));

        $schemaManager = new \Yucca\Component\SchemaManager(array(
            'table0' => array(
                'sharding_strategy'=> 'moduloReturn0',
                'shards' => array('default0')
            ),
        ));
        $schemaManager->setConnectionManager($connectionManager);

        try {
            $this->assertSame($result , $schemaManager->fetchIds('table0', array('firstName'=>array(new \DateTime(),new \DateTime()))));
            $this->fail('Should raise an exception');
        }
        catch (\Exception $exception) {
            $this->assertContains('Don\'t know what to do with criteria firstName', $exception->getMessage());
        }

        $this->assertSame($result , $schemaManager->fetchIds('table0', array('firstName'=>array('Bill','Bob'))));
    }

    public function test_fetchAllMultipleCriteria(){
        $result = array(array('id'=>1),array('id'=>2));
        $connection = $this->getMockBuilder('\Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();
        $connection->expects($this->once())
            ->method('fetchAll')
            ->with('SELECT id FROM `table0` WHERE `firstName` IN (:firstName0,:firstName1) AND `lastName`=:lastName',array(':firstName0'=>'Bill',':firstName1'=>'Bob',':lastName'=>'Jobs'))
            ->will($this->returnValue($result));
        $connectionManager = $this->getMockBuilder('Yucca\Component\ConnectionManager')
            ->disableOriginalConstructor()
            ->getMock();
        $connectionManager->expects($this->once())
            ->method('getConnection')
            ->with('default0')
            ->will($this->returnValue($connection));

        $schemaManager = new \Yucca\Component\SchemaManager(array(
            'table0' => array(
                'sharding_strategy'=> 'moduloReturn0',
                'shards' => array('default0')
            ),
        ));
        $schemaManager->setConnectionManager($connectionManager);

        $this->assertSame($result , $schemaManager->fetchIds('table0', array('firstName'=>array('Bill','Bob'),'lastName'=>'Jobs')));
    }

    public function test_fetchAllEntitiesCriterias(){
        $result = array(array('id'=>1),array('id'=>2));
        $connection = $this->getMockBuilder('\Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();
        $connection->expects($this->once())
            ->method('fetchAll')
            ->with('SELECT id FROM `table0` WHERE `external_id1` IN (:external_id10,:external_id11) AND `external_id2`=:external_id2',array(':external_id10'=>'10',':external_id11'=>'11',':external_id2'=>'2'))
            ->will($this->returnValue($result));
        $connectionManager = $this->getMockBuilder('Yucca\Component\ConnectionManager')
            ->disableOriginalConstructor()
            ->getMock();
        $connectionManager->expects($this->once())
            ->method('getConnection')
            ->with('default0')
            ->will($this->returnValue($connection));

        $schemaManager = new \Yucca\Component\SchemaManager(array(
            'table0' => array(
                'sharding_strategy'=> 'moduloReturn0',
                'shards' => array('default0')
            ),
        ));
        $schemaManager->setConnectionManager($connectionManager);

        $external10 = new \Yucca\Concrete\Model\Base();
        $external11 = new \Yucca\Concrete\Model\Base();
        $external2 = new \Yucca\Concrete\Model\Base();

        $external10->setId(10);
        $external11->setId(11);
        $external2->setId(2);

        $this->assertSame($result , $schemaManager->fetchIds('table0', array('external_id1'=>array($external10, $external11),'external_id2'=>$external2)));
    }

    public function test_remove(){
        $connection = $this->getMockBuilder('\Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();
        $connection->expects($this->once())
            ->method('executeUpdate')
            ->with('DELETE FROM table0 WHERE id = ?',array(0=>1));
        $connectionManager = $this->getMockBuilder('Yucca\Component\ConnectionManager')
            ->disableOriginalConstructor()
            ->getMock();
        $connectionManager->expects($this->once())
            ->method('getConnection')
            ->with('default0')
            ->will($this->returnValue($connection));

        $schemaManager = new \Yucca\Component\SchemaManager(array(
            'table0' => array(
                'sharding_strategy'=> 'moduloReturn0',
                'shards' => array('default0')
            ),
        ));
        $schemaManager->setConnectionManager($connectionManager);

        $this->assertSame($schemaManager, $schemaManager->remove('table0', array('id'=>1)));
    }

    public function test_removeSharded(){
        $connection = $this->getMockBuilder('\Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();
        $connection->expects($this->once())
            ->method('executeUpdate')
            ->with('DELETE FROM table0_0 WHERE id = ?',array(0=>1));
        $connectionManager = $this->getMockBuilder('Yucca\Component\ConnectionManager')
            ->disableOriginalConstructor()
            ->getMock();
        $connectionManager->expects($this->once())
            ->method('getConnection')
            ->with('default0')
            ->will($this->returnValue($connection));

        $schemaManager = new \Yucca\Component\SchemaManager(array(
            'table0' => array(
                'sharding_strategy'=> 'moduloReturn0',
                'shards' => array('default0','default1')
            ),
        ));
        $schemaManager->setConnectionManager($connectionManager);

        $shardingStrategy = $this->getMock('Yucca\Component\ShardingStrategy\ShardingStrategyInterface');
        $shardingStrategy->expects($this->exactly(2))
            ->method('getShardingIdentifier')
            ->will($this->returnValue(0));

        $schemaManager->addShardingStrategy('moduloReturn0',$shardingStrategy);

        $this->assertSame($schemaManager, $schemaManager->remove('table0', array('id'=>1,'sharding_key'=>2)));
    }
}
