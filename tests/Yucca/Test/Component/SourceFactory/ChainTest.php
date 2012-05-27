<?php
namespace Yucca\Test\Component\SourceFactory;

class ChainFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function test_getSource() {
        //Test with properties
        $dataParserMock = $this->getMockBuilder('\Yucca\Component\Source\DataParser\DataParser')
            ->disableOriginalConstructor()
            ->getMock();

        $factory = new \Yucca\Component\SourceFactory\ChainFactory($dataParserMock);

        try {
            $factory->getSource('sourceName', array(),array());
            $this->fail('Should raise an exception');
        } catch(\Exception $exception) {
            $this->assertContains('"sources" must be a non empty array', $exception->getMessage());
        }

        $memcache1 = new \Yucca\Component\Source\Memcache('fake',array('connection_name'=>'fake'));
        $memcache2 = new \Yucca\Component\Source\Memcache('fake',array('connection_name'=>'fake'));

        $sources = array(
            $memcache1,
            $memcache2
        );

        $return = $factory->getSource('sourceName', array(), $sources);
        $this->assertInstanceOf('\Yucca\Component\Source\Chain', $return);

        $reflectionSources = new \ReflectionProperty('Yucca\Component\Source\Chain', 'sources');
        $reflectionDataParser = new \ReflectionProperty('Yucca\Component\Source\Chain', 'dataParser');

        $reflectionSources->setAccessible(true);
        $reflectionDataParser->setAccessible(true);

        $this->assertSame($sources, $reflectionSources->getValue($return));
        $this->assertSame($dataParserMock, $reflectionDataParser->getValue($return));
    }
}