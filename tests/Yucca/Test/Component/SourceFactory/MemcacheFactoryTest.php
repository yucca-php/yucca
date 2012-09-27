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

class MemcacheFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function test_getSource() {
        //Test with properties
        $dataParserMock = $this->getMockBuilder('\Yucca\Component\Source\DataParser\DataParser')
            ->disableOriginalConstructor()
            ->getMock();
        $connectionManager = $this->getMockBuilder('\Yucca\Component\ConnectionManager')
                                ->disableOriginalConstructor()
                                ->getMock();
        $factory = new \Yucca\Component\SourceFactory\MemcacheFactory($connectionManager,$dataParserMock);

        try {
            $factory->getSource('sourceName', array());
            $this->fail('Should raise an exception');
        } catch(\Exception $exception) {
            $this->assertContains('Configuration array must contain a \'connection_name\' key', $exception->getMessage());
        }

        $return = $factory->getSource('sourceName', array('connection_name'=>'fakePool'));
        $this->assertInstanceOf('\Yucca\Component\Source\Memcache', $return);

        $reflectionDataParser = new \ReflectionProperty('Yucca\Component\Source\Memcache', 'dataParser');
        $reflectionDataParser->setAccessible(true);
        $this->assertSame($dataParserMock, $reflectionDataParser->getValue($return));
    }
}
