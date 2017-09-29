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

class ChainFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function test_getSource()
    {
        //Test with properties
        $dataParserMock = $this->getMockBuilder('\Yucca\Component\Source\DataParser\DataParser')
            ->disableOriginalConstructor()
            ->getMock();

        $factory = new \Yucca\Component\SourceFactory\ChainFactory($dataParserMock);

        try {
            $factory->getSource('sourceName', array(), array());
            $this->fail('Should raise an exception');
        } catch (\Exception $exception) {
            $this->assertContains('"sources" must be a non empty array', $exception->getMessage());
        }

        $memcache1 = new \Yucca\Component\Source\Memcache('fake', array('connection_name'=>'fake'));
        $memcache2 = new \Yucca\Component\Source\Memcache('fake', array('connection_name'=>'fake'));

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
