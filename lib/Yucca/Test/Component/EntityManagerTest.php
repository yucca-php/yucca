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

class EntityManagerTest extends TestCase
{

    public function test_load()
    {
        //Initialize entity manager
        $selectorManagerMock = $this->getMockBuilder('\Yucca\Component\SelectorManager')
            ->disableOriginalConstructor()
            ->getMock();

        $mappingManagerMock = $this->getMockBuilder('\Yucca\Component\MappingManager')
            ->disableOriginalConstructor()
            ->getMock();

        $entityManager = new \Yucca\Component\EntityManager('strange_identifier');
        $entityManager->setMappingManager($mappingManagerMock);
        $entityManager->setSelectorManager($selectorManagerMock);

        //Load with wrong class
        try {
            $entityManager->load('\DateTime', 1);
            $this->fail('Should raise an exception');
        } catch (\Exception $exception) {
            $this->assertContains('Entity class \DateTime must implement \Yucca\Model\ModelInterface.', $exception->getMessage());
        }
        //Load with class that does not exists
        try {
            $entityManager->load('\FakeClass', 1);
            $this->fail('Should raise an exception');
        } catch (\Exception $exception) {
            $this->assertContains('Entity class \FakeClass not found.', $exception->getMessage());
        }

        //Load without sharding key
        $model = $entityManager->load('\Yucca\Test\Concrete\Model\Base', 1);
        $this->assertSame($selectorManagerMock, $model->getYuccaSelectorManager());
        $this->assertSame($mappingManagerMock, $model->getYuccaMappingManager());
        $this->assertSame(array('strange_identifier'=>1), $model->getYuccaIdentifier());
        $this->assertSame($entityManager, $model->getYuccaEntityManager());
        $this->assertInstanceOf('\Yucca\Test\Concrete\Model\Base', $model);

        //Load with sharding key
        $model = $entityManager->load('\Yucca\Test\Concrete\Model\Base', 2, 4);
        $this->assertSame(array('strange_identifier'=>2), $model->getYuccaIdentifier());
        $this->assertSame(4, $model->getYuccaShardingKey());
        $this->assertInstanceOf('\Yucca\Test\Concrete\Model\Base', $model);
    }

    public function test_save()
    {
        //Initialize entity manager
        $selectorManagerMock = $this->getMockBuilder('\Yucca\Component\SelectorManager')
            ->disableOriginalConstructor()
            ->getMock();

        $mappingManagerMock = $this->getMockBuilder('\Yucca\Component\MappingManager')
            ->disableOriginalConstructor()
            ->getMock();

        $entityManager = new \Yucca\Component\EntityManager('strange_identifier');
        $entityManager->setMappingManager($mappingManagerMock);
        $entityManager->setSelectorManager($selectorManagerMock);

        $modelMock = $this->createMock('\Yucca\Model\ModelAbstract');
        $modelMock->expects($this->once())
            ->method('save')
            ->will($this->returnValue(null));
        $modelMock->expects($this->once())
            ->method('setYuccaMappingManager')
            ->will($this->returnValue($modelMock));
        $modelMock->expects($this->once())
            ->method('setYuccaEntityManager')
            ->will($this->returnValue($modelMock));
        $modelMock->expects($this->once())
            ->method('setYuccaSelectorManager')
            ->will($this->returnValue($modelMock));

        $return = $entityManager->save($modelMock);
        $this->assertSame($return, $entityManager);
    }

    public function test_remove()
    {
        //Initialize entity manager
        $selectorManagerMock = $this->getMockBuilder('\Yucca\Component\SelectorManager')
            ->disableOriginalConstructor()
            ->getMock();

        $mappingManagerMock = $this->getMockBuilder('\Yucca\Component\MappingManager')
            ->disableOriginalConstructor()
            ->getMock();

        $entityManager = new \Yucca\Component\EntityManager('strange_identifier');
        $entityManager->setMappingManager($mappingManagerMock);
        $entityManager->setSelectorManager($selectorManagerMock);

        $modelMock = $this->createMock('\Yucca\Model\ModelAbstract');
        $modelMock->expects($this->once())
            ->method('remove')
            ->will($this->returnValue(null));
        $modelMock->expects($this->once())
            ->method('setYuccaMappingManager')
            ->will($this->returnValue($modelMock));
        $modelMock->expects($this->once())
            ->method('setYuccaEntityManager')
            ->will($this->returnValue($modelMock));
        $modelMock->expects($this->once())
            ->method('setYuccaSelectorManager')
            ->will($this->returnValue($modelMock));

        $return = $entityManager->remove($modelMock);
        $this->assertSame($return, $entityManager);
    }

    public function test_reset()
    {
        //Initialize entity manager
        $entityManager = new \Yucca\Component\EntityManager('strange_identifier');

        $newIdentifier = array('id'=>5);
        $modelMock = $this->createMock('\Yucca\Model\ModelAbstract');
        $modelMock->expects($this->once())
            ->method('reset')
            ->with($this->equalTo($newIdentifier))
            ->will($this->returnValue(null));

        //Check call only once
        $return = $entityManager->resetModel($modelMock, $newIdentifier);
        $this->assertSame($return, $entityManager);

        //Check that new identifiers are well set
        $model = new \Yucca\Test\Concrete\Model\Base();
        $entityManager->resetModel($model, $newIdentifier);

        $this->assertSame($newIdentifier, $model->getYuccaIdentifier());
    }
}
