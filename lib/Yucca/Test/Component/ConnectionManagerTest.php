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

class ConnectionManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return mixed
     */
    public function test_construct()
    {
        //Loop on invalid types
        foreach (array(1,'1',true,null) as $type) {
            try {
                new \Yucca\Component\ConnectionManager($type);
            } catch (\TypeError $exception) {
                continue;
            }

            $this->fail('First argument of \Yucca\Component\ConnectionManager must be an array');
        }

        //Correct constructor
        new \Yucca\Component\ConnectionManager(array());
    }

    /**
     * @return mixed
     */
    public function test_addConnectionFactory()
    {
        $connectionManager = new \Yucca\Component\ConnectionManager(array());

        try {
            $method = new \ReflectionMethod('\Yucca\Component\ConnectionManager', 'getConnectionFactory');
            $method->setAccessible(true);

            $method->invokeArgs($connectionManager, array('fake'));
            $this->fail('\Yucca\Component\ConnectionManager::getConnectionFactory should raise an exception');
        } catch (\InvalidArgumentException $exception) {
            $this->assertContains('fake', $exception->getMessage());
        }

        $connectionFactoryMock = $this->createMock('\Yucca\Component\ConnectionFactory\ConnectionFactoryInterface');

        $this->assertSame(
            $connectionManager,
            $connectionManager->addConnectionFactory(
                'fake',
                $connectionFactoryMock
            )
        );

        $this->assertSame(
            $connectionFactoryMock,
            $method->invokeArgs(
                $connectionManager,
                array('fake')
            )
        );
    }

    public function test_getConnection()
    {
        //Connection not configured
        $connectionsConfig = array();
        $connectionManager = new \Yucca\Component\ConnectionManager($connectionsConfig);

        try {
            $connectionManager->getConnection('fake');
            $this->fail('Should raise an exception');
        } catch (\InvalidArgumentException $exception) {
            $this->assertContains('fake', $exception->getMessage());
        }

        //Connection not well configured
        $connectionsConfig = array(
            'fake' => array(),
        );
        $connectionManager = new \Yucca\Component\ConnectionManager($connectionsConfig);
        try {
            $connectionManager->getConnection('fake');
            $this->fail('Should raise an exception');
        } catch (\InvalidArgumentException $exception) {
            $this->assertNotContains('fake', $exception->getMessage());
        }

        //Connection without factory
        $connectionsConfig = array(
            'fake' => array(
                'type'=>'unknown'
            )
        );
        $connectionManager = new \Yucca\Component\ConnectionManager($connectionsConfig);
        try {
            $connectionManager->getConnection('fake');
            $this->fail('Should raise an exception');
        } catch (\InvalidArgumentException $exception) {
            $this->assertContains('unknown', $exception->getMessage());
        }

        //Connection with factory
        $fakeConfiguration = array(
            'type'=>'mock',
            'param1'=>1,
            'param2'=>2,
            'param3'=>3,
        );

        $connectionMock = $this->createMock('\Memcache');
        $connectionFactoryMock = $this->createMock('\Yucca\Component\ConnectionFactory\ConnectionFactoryInterface');
        $connectionFactoryMock->expects($this->once())
            ->method('getConnection')
            ->with($this->equalTo($fakeConfiguration))
            ->will($this->returnValue($connectionMock));
        $connectionsConfig = array(
            'fake' => $fakeConfiguration,
        );
        $connectionManager = new \Yucca\Component\ConnectionManager($connectionsConfig);
        $connectionManager->addConnectionFactory('mock', $connectionFactoryMock);
        $this->assertSame($connectionMock, $connectionManager->getConnection('fake'));
        //To see if real singleton
        $this->assertSame($connectionMock, $connectionManager->getConnection('fake'));
    }
}
