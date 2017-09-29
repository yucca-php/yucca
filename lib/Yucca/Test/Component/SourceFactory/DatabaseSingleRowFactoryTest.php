<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yucca\Test\Component\SourceFactory;

class DatabaseSingleRowFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function test_getSource()
    {
        //Test with properties
        $dataParserMock = $this->getMockBuilder('\Yucca\Component\Source\DataParser\DataParser')
            ->disableOriginalConstructor()
            ->getMock();
        $schemaManager = $this->getMockBuilder('\Yucca\Component\SchemaManager')
            ->disableOriginalConstructor()
            ->getMock();
        $connectionManager = $this->getMockBuilder('\Yucca\Component\ConnectionManager')
            ->disableOriginalConstructor()
            ->getMock();
        $factory = new \Yucca\Component\SourceFactory\DatabaseSingleRowFactory($schemaManager, $connectionManager, $dataParserMock);

        try {
            $factory->getSource('sourceName', array());
            $this->fail('Should raise an exception');
        } catch (\Exception $exception) {
            $this->assertContains('Configuration array must contain a \'table_name\' key', $exception->getMessage());
        }

        $return = $factory->getSource('sourceName', array('table_name'=>'fakeTable'));
        $this->assertInstanceOf('\Yucca\Component\Source\DatabaseSingleRow', $return);

        $reflectionSchemaManager = new \ReflectionProperty('Yucca\Component\Source\DatabaseSingleRow', 'schemaManager');
        $reflectionConnectionManager = new \ReflectionProperty('Yucca\Component\Source\DatabaseSingleRow', 'connectionManager');
        $reflectionDataParser = new \ReflectionProperty('Yucca\Component\Source\DatabaseSingleRow', 'dataParser');

        $reflectionSchemaManager->setAccessible(true);
        $reflectionConnectionManager->setAccessible(true);
        $reflectionDataParser->setAccessible(true);

        $this->assertSame($schemaManager, $reflectionSchemaManager->getValue($return));
        $this->assertSame($connectionManager, $reflectionConnectionManager->getValue($return));
        $this->assertSame($dataParserMock, $reflectionDataParser->getValue($return));
    }
}
