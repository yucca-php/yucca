<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Test\Model;

use PHPUnit\Framework\TestCase;
use Yucca\Component\Mapping\Mapper;
use Yucca\Component\MappingManager;
use Yucca\Component\Source\Exception\NoDataException;
use Yucca\Test\Concrete\Model\Base;

class ModelAbstractTest extends TestCase
{
    /**
     * @return mixed
     */
    public function test_setYuccaMappingManager()
    {
        $mappingManagerMock = $this->getMockBuilder('\Yucca\Component\MappingManager')
            ->disableOriginalConstructor()
            ->getMock();

        $model = new \Yucca\Test\Concrete\Model\Base();
        $model->setYuccaMappingManager($mappingManagerMock);
        $this->assertSame($mappingManagerMock, $model->getYuccaMappingManager());
    }

    /**
     * @return mixed
     */
    public function test_setYuccaSelectorManager()
    {
        $selectorManagerMock = $this->getMockBuilder('\Yucca\Component\SelectorManager')
            ->disableOriginalConstructor()
            ->getMock();

        $model = new \Yucca\Test\Concrete\Model\Base();
        $model->setYuccaSelectorManager($selectorManagerMock);
        $this->assertSame($selectorManagerMock, $model->getYuccaSelectorManager());
    }

    /**
     * @return mixed
     */
    public function test_setYuccaEntityManager()
    {
        $entityManagerMock = $this->getMockBuilder('\Yucca\Component\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $model = new \Yucca\Test\Concrete\Model\Base();
        $model->setYuccaEntityManager($entityManagerMock);
        $this->assertSame($entityManagerMock, $model->getYuccaEntityManager());
    }

    /**
     * @return mixed
     */
    public function testRefresh()
    {
        $modelMock = $this->getMockBuilder(Base::class)->getMock();

        $mappingManagerMock = $this->getMockBuilder('\Yucca\Component\MappingManager')
            ->disableOriginalConstructor()
            ->getMock();
        $selectorManagerMock = $this->getMockBuilder('\Yucca\Component\SelectorManager')
            ->disableOriginalConstructor()
            ->getMock();
        $entityManagerMock = $this->getMockBuilder('\Yucca\Component\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $entityManagerMock->expects($this->once())->method('refresh')->with($modelMock);


        $model = new \Yucca\Test\Concrete\Model\Properties();
        $reflectionYuccaInitialized = new \ReflectionProperty(get_class($model), 'yuccaInitialized');
        $reflectionYuccaInitialized->setAccessible(true);
        $reflectionYuccaInitialized->setValue($model, array('first'=>true, 'second'=>true));
        $reflectionYuccaInitialized = new \ReflectionProperty(get_class($model), 'first');
        $reflectionYuccaInitialized->setAccessible(true);
        $reflectionYuccaInitialized->setValue($model, $modelMock);

        $model->refresh($mappingManagerMock, $selectorManagerMock, $entityManagerMock);
        $this->assertSame($mappingManagerMock, $model->getYuccaMappingManager());
        $this->assertSame($selectorManagerMock, $model->getYuccaSelectorManager());
        $this->assertSame($entityManagerMock, $model->getYuccaEntityManager());
    }

    /**
     * @return mixed
     */
    public function testSleep()
    {
        $model = new \Yucca\Test\Concrete\Model\Properties();
        $this->assertSame(array('yuccaInitialized', 'yuccaProperties', 'yuccaIdentifier', 'yuccaShardingKey', 'id', 'first', 'second', 'third'), $model->__sleep());
    }

    /**
     * @return mixed
     */
    public function test_setYuccaIdentifier()
    {
        $yuccaIdentifier = 3;
        $model = new \Yucca\Test\Concrete\Model\Base();
        $return = $model->setYuccaIdentifier($yuccaIdentifier);
        $this->assertSame($model, $return);
        $this->assertSame($yuccaIdentifier, $return->getYuccaIdentifier());
    }

    /**
     * @return mixed
     */
    public function testEnsureExistNoYuccaIdentifiers()
    {
        $this->expectException(NoDataException::class);
        $this->expectExceptionMessage('Yucca\Test\Concrete\Model\Base doesn\'t seems to be saved in database');

        $model = new \Yucca\Test\Concrete\Model\Base();
        $model->ensureExist();
    }

    /**
     * @return mixed
     */
    public function testEnsureExist()
    {
        $mapperMock = $this->getMockBuilder(Mapper::class)->disableOriginalConstructor()->getMock();
        $mapperMock->expects($this->once())->method('load')->with(array('id'=>10), 'second')->willReturn(array('second'=>2, 'third'=>'3'));
        $mappingManagerMock = $this->getMockBuilder(MappingManager::class)->disableOriginalConstructor()->getMock();
        $mappingManagerMock->method('getMapper')->willReturn($mapperMock);

        $model = new \Yucca\Test\Concrete\Model\Properties();
        $model->setYuccaIdentifier(array('id'=>10));
        $model->setYuccaMappingManager($mappingManagerMock);
        $reflectionYuccaInitialized = new \ReflectionProperty(get_class($model), 'yuccaInitialized');
        $reflectionYuccaInitialized->setAccessible(true);
        $reflectionYuccaInitialized->setValue($model, array('id'=>true, 'first'=>true));

        $model->ensureExist();
    }

    /**
     * @return mixed
     */
    public function testEnsureExistNoData()
    {
        $mapperMock = $this->getMockBuilder(Mapper::class)->disableOriginalConstructor()->getMock();
        $mapperMock->expects($this->once())->method('load')->willThrowException(new \Yucca\Component\Source\Exception\NoDataException(''));
        $mappingManagerMock = $this->getMockBuilder(MappingManager::class)->disableOriginalConstructor()->getMock();
        $mappingManagerMock->method('getMapper')->willReturn($mapperMock);

        $this->expectException(\Yucca\Component\Source\Exception\NoDataException::class);
        $this->expectExceptionMessage('Yucca\Test\Concrete\Model\Properties doesn\'t seems to exist with identifiers : array (
  \'id\' => 10,
)');

        $model = new \Yucca\Test\Concrete\Model\Properties();
        $model->setYuccaIdentifier(array('id'=>10));
        $model->setYuccaMappingManager($mappingManagerMock);
        $reflectionYuccaInitialized = new \ReflectionProperty(get_class($model), 'yuccaInitialized');
        $reflectionYuccaInitialized->setAccessible(true);
        $reflectionYuccaInitialized->setValue($model, array('id'=>true, 'first'=>true));

        $model->ensureExist();
    }

    public function test_reset()
    {
        $model = new \Yucca\Test\Concrete\Model\Properties();
        $model->setFirst(1)
            ->setSecond(2)
            ->setThird(3);


        //Test reset with only one property configured
        $model->setYuccaProperties(array('first'));

        $model->reset(array('id'=>123456));
        $this->assertSame(array('id'=>123456), $model->getYuccaIdentifier());
        $this->assertSame(null, $model->getFirst());
        $this->assertSame(2, $model->getSecond());
        $this->assertSame(3, $model->getThird());

        //Test reset all properties
        $model->setYuccaProperties(array('first','second','third'));

        $model->reset(array('id'=>123456));
        $this->assertSame(null, $model->getFirst());
        $this->assertSame(null, $model->getSecond());
        $this->assertSame(null, $model->getThird());
    }

    public function test_save()
    {
        $model = new \Yucca\Test\Concrete\Model\Properties();

        try {
            $model->save();
            $this->fail('Should raise an exception');
        } catch (\Exception $exception) {
            $this->assertContains('Mapping manager', $exception->getMessage());
        }

        $model->setFirst(1);
        $model->setSecond(2);
        $model->setThird(3);

        $mapperMock = $this->getMockBuilder('\Yucca\Component\Mapping\Mapper')
            ->disableOriginalConstructor()
            ->getMock();
        $mapperMock->expects($this->once())
                    ->method('save')
                    ->with($this->equalTo(null), $this->equalTo(array('id'=>null,'first'=>1,'second'=>2,'third'=>3)))
                    ->willReturn(array('id'=>1000));
        $mapperMock->expects($this->once())
                    ->method('getPropertiesToRefreshAfterSave')
                    ->willReturn(array('second'));

        $mappingManagerMock = $this->getMockBuilder('\Yucca\Component\MappingManager')
            ->disableOriginalConstructor()
            ->getMock();
        $mappingManagerMock->expects($this->once())
            ->method('getMapper')
            ->with('Yucca\Test\Concrete\Model\Properties')
            ->will($this->returnValue($mapperMock));

        $model->setYuccaMappingManager($mappingManagerMock);

        $return = $model->save();

        $this->assertSame($model, $return);
        $this->assertSame(1000, $model->getId());
        $this->assertSame(array('id'=>1000), $model->getYuccaIdentifier());

        $reflection = new \ReflectionProperty(get_class($model), "second");
        $reflection->setAccessible(true);
        $this->assertNull($reflection->getValue($model));
        $reflection = new \ReflectionProperty(get_class($model), "yuccaInitialized");
        $reflection->setAccessible(true);
        $this->assertFalse(isset($reflection->getValue($model)['second']));
        $this->assertTrue(isset($reflection->getValue($model)['first']));
    }

    public function test_hydrate()
    {
        $model = new \Yucca\Test\Concrete\Model\Properties();

        //Assert nothing is done when no mapper set
        $return = $model->getFirst();
        $this->assertSame(null, $return);

        //Create mapper and mapping manager
        $mapperMock = $this->getMockBuilder('\Yucca\Component\Mapping\Mapper')
            ->disableOriginalConstructor()
            ->getMock();
        $mapperMock->expects($this->once())
            ->method('load')
            ->with($this->equalTo(array('id'=>1)), $this->equalTo('first'))
            ->will($this->returnValue(array('first'=>1,'second'=>2,'third'=>3)));

        $mappingManagerMock = $this->getMockBuilder('\Yucca\Component\MappingManager')
            ->disableOriginalConstructor()
            ->getMock();
        $mappingManagerMock->expects($this->once())
            ->method('getMapper')
            ->with('Yucca\Test\Concrete\Model\Properties')
            ->will($this->returnValue($mapperMock));

        //
        $model = new \Yucca\Test\Concrete\Model\Properties();
        $model->setYuccaIdentifier(array('id'=>1))
                ->setYuccaMappingManager($mappingManagerMock);

        $returnFirst = $model->getFirst();
        $returnSecond = $model->getSecond();
        $returnThird = $model->getThird();

        $this->assertSame(1, $returnFirst);
        $this->assertSame(2, $returnSecond);
        $this->assertSame(3, $returnThird);
    }

    /**
     *
     */
    public function test_remove()
    {
        $model = new \Yucca\Test\Concrete\Model\Base();
        try {
            $model->remove();
            $this->fail('Should raise an exception');
        } catch (\Exception $e) {
            $this->assertContains("Mapping manager isn't set", $e->getMessage());
        }

        //Create mapper and mapping manager
        $mapperMock = $this->getMockBuilder('\Yucca\Component\Mapping\Mapper')
            ->disableOriginalConstructor()
            ->getMock();
        $mapperMock->expects($this->once())
            ->method('remove')
            ->with($this->equalTo(array('id'=>1,'sharding_key'=>129)));

        $mappingManagerMock = $this->getMockBuilder('\Yucca\Component\MappingManager')
            ->disableOriginalConstructor()
            ->getMock();
        $mappingManagerMock->expects($this->once())
            ->method('getMapper')
            ->with('Yucca\Test\Concrete\Model\Base')
            ->will($this->returnValue($mapperMock));

        $model->setYuccaIdentifier(array('id'=>1,'sharding_key'=>129))
            ->setYuccaMappingManager($mappingManagerMock);

        $this->assertSame($model, $model->remove());
        $this->assertSame(array(), $model->getYuccaIdentifier());
    }
}
