<?php
namespace Yucca\Test\Validator\Constraints;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\MissingOptionsException;
use Yucca\Validator\Constraints\UniqueEntity;

/**
 * Class UniqueEntityTest
 * @package Yucca\Test\Validator\Constraints
 */
class UniqueEntityTest extends TestCase
{
    /**
     *
     */
    public function testUniqueEntity()
    {
        $ue = new UniqueEntity(array(
            'message' => 'test message',
            'service' => 'test service',
            'selector' => 'test selector',
            'fields' => array('field1', 'field2'),
            'errorPath' => 'test error path',
            'ignoreNull' => false,
        ));

        $this->assertEquals(array('fields'), $ue->getRequiredOptions());
        $this->assertEquals('fields', $ue->getDefaultOption());
        $this->assertEquals('test service', $ue->validatedBy());
        $this->assertEquals('class', $ue->getTargets());
        $this->assertEquals('test message', $ue->message);
        $this->assertEquals('test selector', $ue->selector);
        $this->assertEquals('test error path', $ue->errorPath);
        $this->assertEquals(false, $ue->ignoreNull);
        $this->assertEquals(array('field1', 'field2'), $ue->fields);
    }

    /**
     *
     */
    public function testDefaultUniqueEntity()
    {
        $ue = new UniqueEntity(array('fields'=>array('email')));

        $this->assertEquals(array('fields'), $ue->getRequiredOptions());
        $this->assertEquals('fields', $ue->getDefaultOption());
        $this->assertEquals('yucca.validator.unique', $ue->validatedBy());
        $this->assertEquals('class', $ue->getTargets());
        $this->assertEquals('This value is already used.', $ue->message);
        $this->assertEquals(null, $ue->selector);
        $this->assertEquals(null, $ue->errorPath);
        $this->assertEquals(true, $ue->ignoreNull);
        $this->assertEquals(array('email'), $ue->fields);
    }

    /**
     *
     */
    public function testUniqueEntityWithoutArgs()
    {
        $this->expectException(MissingOptionsException::class);
        new UniqueEntity();
    }
}
