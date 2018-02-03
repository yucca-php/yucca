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

class MappingManagerTest extends TestCase
{
    public function test_getMapper()
    {
        $className = 'Yucca\Test\Concrete\Model\Properties';
        $configuration = array(
            $className => array(

            )
        );

        //Test without model class
        $mappingManager = new \Yucca\Component\MappingManager(array());

        try {
            $unknownClass = '\Unknown\model';
            $mappingManager->getMapper($unknownClass);
            $this->fail('Should raise an exception');
        } catch (\RuntimeException $exception) {
            $this->assertContains($unknownClass, $exception->getMessage());
        }

        //Test with properties
        $sourceManagerMock = $this->getMockBuilder('\Yucca\Component\SourceManager')
            ->disableOriginalConstructor()
            ->getMock();
        $mappingManager = new \Yucca\Component\MappingManager($configuration);
        $mappingManager->setSourceManager($sourceManagerMock);
        $mapper1 = $mappingManager->getMapper($className);

        $this->assertInstanceOf('Yucca\Component\Mapping\Mapper', $mapper1);

        //Ensure mapper multiton
        $mapper2 = $mappingManager->getMapper($className);
        $this->assertSame($mapper1, $mapper2);

        //Check mapper properties

        $mapperSourceManager = new \ReflectionProperty('Yucca\Component\Mapping\Mapper', 'sourceManager');
        $mapperSourceManager->setAccessible(true);

        $this->assertSame($sourceManagerMock, $mapperSourceManager->getValue($mapper1));
    }
}
