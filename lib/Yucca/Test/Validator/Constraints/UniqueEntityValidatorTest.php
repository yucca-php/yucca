<?php
namespace Yucca\Test\Validator\Constraints;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;
use Yucca\Component\EntityManager;
use Yucca\Component\SelectorManager;
use Yucca\Test\Concrete\Model\Base;
use Yucca\Test\Concrete\Model\Properties;
use Yucca\Validator\Constraints\UniqueEntity;
use Yucca\Validator\Constraints\UniqueEntityValidator;

/**
 * Class UniqueEntityValidatorTest
 * @package Yucca\Test\Validator\Constraints
 */
class UniqueEntityValidatorTest extends TestCase
{
    /**
     *
     */
    public function testUniqueEntityValidatorWrongConstraint()
    {
        $selectorManagerMock = $this->getMockBuilder(SelectorManager::class)->disableOriginalConstructor()->getMock();
        $entityManagerMock = $this->getMockBuilder(EntityManager::class)->getMock();
        $ueMock = $this->getMockBuilder(UniqueEntity::class)->disableOriginalConstructor()->getMock();
        $entityMock = $this->getMockBuilder(Properties::class)->getMock();
        $contextMock = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();

        $this->expectException(ConstraintDefinitionException::class);

        $uev = new UniqueEntityValidator($selectorManagerMock, $entityManagerMock);
        $uev->initialize($contextMock);
        $uev->validate($entityMock, $ueMock);
    }

    /**
     *
     */
    public function testUniqueEntityValidatorNothingFound()
    {
        $selectorMock = $this->getMockBuilder(\Yucca\Test\Concrete\Selector\Properties::class)->disableOriginalConstructor()->getMock();
        $selectorMock->expects($this->once())->method('addFirstCriteria')->with('firstValue')->willReturn($selectorMock);
        $selectorManagerMock = $this->getMockBuilder(SelectorManager::class)->disableOriginalConstructor()->getMock();
        $selectorManagerMock->expects($this->once())->method('getSelector')->with('Yucca\Test\Concrete\Selector\Properties')->willReturn($selectorMock);
        $entityManagerMock = $this->getMockBuilder(EntityManager::class)->getMock();
        $ueMock = $this->getMockBuilder(UniqueEntity::class)->disableOriginalConstructor()->getMock();
        $ueMock->fields=array('first');
        $ueMock->selector='Yucca\Test\Concrete\Selector\Properties';
        $entityMock = $this->getMockBuilder(Properties::class)->getMock();
        $entityMock->expects($this->once())->method('getFirst')->willReturn('firstValue');
        $contextMock = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();

        $uev = new UniqueEntityValidator($selectorManagerMock, $entityManagerMock);
        $uev->initialize($contextMock);
        $uev->validate($entityMock, $ueMock);
    }

    /**
     *
     */
    public function testUniqueEntityValidatorNullAndIgnore()
    {
        $selectorMock = $this->getMockBuilder(\Yucca\Test\Concrete\Selector\Properties::class)->disableOriginalConstructor()->getMock();
        $selectorManagerMock = $this->getMockBuilder(SelectorManager::class)->disableOriginalConstructor()->getMock();
        $selectorManagerMock->expects($this->once())->method('getSelector')->with('Yucca\Test\Concrete\Selector\Properties')->willReturn($selectorMock);
        $entityManagerMock = $this->getMockBuilder(EntityManager::class)->getMock();
        $ueMock = $this->getMockBuilder(UniqueEntity::class)->disableOriginalConstructor()->getMock();
        $ueMock->fields=array('first');
        $ueMock->selector='Yucca\Test\Concrete\Selector\Properties';
        $entityMock = $this->getMockBuilder(Properties::class)->getMock();
        $entityMock->expects($this->once())->method('getFirst')->willReturn(null);
        $contextMock = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();

        $uev = new UniqueEntityValidator($selectorManagerMock, $entityManagerMock);
        $uev->initialize($contextMock);
        $uev->validate($entityMock, $ueMock);
    }

    /**
     *
     */
    public function testUniqueEntityValidatorNoSelectorSpecified()
    {
        $selectorMock = $this->getMockBuilder(\Yucca\Test\Concrete\Selector\Properties::class)->disableOriginalConstructor()->getMock();
        $selectorMock->expects($this->once())->method('addFirstCriteria')->with('firstValue')->willReturn($selectorMock);
        $selectorMock->method('count')->willReturn(1);
        $selectorMock->method('current')->willReturn(101);
        $selectorManagerMock = $this->getMockBuilder(SelectorManager::class)->disableOriginalConstructor()->getMock();
        $selectorManagerMock->expects($this->once())->method('getSelector')->with('Yucca\Test\Concrete\Selector\Properties')->willReturn($selectorMock);
        $entityMock = new Properties();
        $entityMock->setFirst('firstValue');
        $iteratorEntityMock = new Properties();
        $iteratorEntityMock->setId(101);
        $iteratorEntityMock->setFirst('firstValue');
        $entityManagerMock = $this->getMockBuilder(EntityManager::class)->getMock();
        $entityManagerMock->method('resetModel')->with($iteratorEntityMock, 101);
        $entityManagerMock->method('load')->with(get_class($iteratorEntityMock), 101, null)->willReturn($iteratorEntityMock);
        $ueMock = $this->getMockBuilder(UniqueEntity::class)->disableOriginalConstructor()->getMock();
        $ueMock->fields=array('first');

        $contextMock = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();

        $uev = new UniqueEntityValidator($selectorManagerMock, $entityManagerMock);
        $uev->initialize($contextMock);
        $uev->validate($entityMock, $ueMock);
    }

    /**
     *
     */
    public function testUniqueEntityValidatorNoSelectorMethod()
    {
        $selectorMock = $this->getMockBuilder(\Yucca\Test\Concrete\Selector\Properties::class)->disableOriginalConstructor()->getMock();
        $selectorManagerMock = $this->getMockBuilder(SelectorManager::class)->disableOriginalConstructor()->getMock();
        $selectorManagerMock->expects($this->once())->method('getSelector')->with('Yucca\Test\Concrete\Selector\Properties')->willReturn($selectorMock);
        $entityMock = new Properties();
        $entityMock->setFirst('firstValue');
        $iteratorEntityMock = new Properties();
        $iteratorEntityMock->setId(101);
        $iteratorEntityMock->setFirst('firstValue');
        $entityManagerMock = $this->getMockBuilder(EntityManager::class)->getMock();
        $entityManagerMock->method('resetModel')->with($iteratorEntityMock, 101);
        $entityManagerMock->method('load')->with(get_class($iteratorEntityMock), 101, null)->willReturn($iteratorEntityMock);
        $ueMock = $this->getMockBuilder(UniqueEntity::class)->disableOriginalConstructor()->getMock();
        $ueMock->fields=array('inexistant');

        $this->expectException(\Symfony\Component\Validator\Exception\ConstraintDefinitionException::class);
        $this->expectExceptionMessage('The field \'inexistant\' is not mapped');

        $uev = new UniqueEntityValidator($selectorManagerMock, $entityManagerMock);
        $uev->validate($entityMock, $ueMock);
    }

    /**
     *
     */
    public function testUniqueEntityValidatorFoundSameId()
    {
        $selectorMock = $this->getMockBuilder(\Yucca\Test\Concrete\Selector\Properties::class)->disableOriginalConstructor()->getMock();
        $selectorMock->expects($this->once())->method('addFirstCriteria')->with('firstValue')->willReturn($selectorMock);
        $selectorMock->method('count')->willReturn(1);
        $selectorMock->method('current')->willReturn(101);
        $selectorManagerMock = $this->getMockBuilder(SelectorManager::class)->disableOriginalConstructor()->getMock();
        $selectorManagerMock->expects($this->once())->method('getSelector')->with('Yucca\Test\Concrete\Selector\Properties')->willReturn($selectorMock);
        $entityMock = $this->getMockBuilder(Properties::class)->getMock();
        $entityMock->expects($this->once())->method('getFirst')->willReturn('firstValue');
        $entityManagerMock = $this->getMockBuilder(EntityManager::class)->getMock();
        $entityManagerMock->method('resetModel')->with($entityMock, 101);
        $entityManagerMock->method('load')->with(get_class($entityMock), 101, null)->willReturn(clone $entityMock);
        $ueMock = $this->getMockBuilder(UniqueEntity::class)->disableOriginalConstructor()->getMock();
        $ueMock->fields=array('first');
        $ueMock->selector='Yucca\Test\Concrete\Selector\Properties';

        $contextMock = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();

        $uev = new UniqueEntityValidator($selectorManagerMock, $entityManagerMock);
        $uev->initialize($contextMock);
        $uev->validate($entityMock, $ueMock);
    }

    /**
     *
     */
    public function testUniqueEntityValidatorWithError()
    {
        $selectorMock = $this->getMockBuilder(\Yucca\Test\Concrete\Selector\Properties::class)->disableOriginalConstructor()->getMock();
        $selectorMock->expects($this->once())->method('addFirstCriteria')->with('firstValue')->willReturn($selectorMock);
        $selectorMock->method('count')->willReturn(1);
        $selectorMock->method('current')->willReturn(101);
        $selectorManagerMock = $this->getMockBuilder(SelectorManager::class)->disableOriginalConstructor()->getMock();
        $selectorManagerMock->expects($this->once())->method('getSelector')->with('Yucca\Test\Concrete\Selector\Properties')->willReturn($selectorMock);
        $entityMock = $this->getMockBuilder(Properties::class)->getMock();
        $entityMock->expects($this->once())->method('getFirst')->willReturn('firstValue');
        $entityManagerMock = $this->getMockBuilder(EntityManager::class)->getMock();
        $entityManagerMock->method('resetModel')->with($entityMock, 101);
        $entityManagerMock->method('load')->with(get_class($entityMock), 101, null)->willReturn(clone $entityMock);
        $entityMock->method('getId')->willReturn(102);
        $ueMock = $this->getMockBuilder(UniqueEntity::class)->disableOriginalConstructor()->getMock();
        $ueMock->fields=array('first');
        $ueMock->selector='Yucca\Test\Concrete\Selector\Properties';
        $constraintViolationBuilderMock = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)->getMock();
        $constraintViolationBuilderMock->expects($this->once())->method('atPath')->with('first')->willReturn($constraintViolationBuilderMock);
        $constraintViolationBuilderMock->expects($this->once())->method('addViolation');
        $contextMock = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();
        $contextMock->expects($this->once())->method('buildViolation')->with('This value is already used.')->willReturn($constraintViolationBuilderMock);

        $uev = new UniqueEntityValidator($selectorManagerMock, $entityManagerMock);
        $uev->initialize($contextMock);
        $uev->validate($entityMock, $ueMock);
    }

    /**
     *
     */
    public function testUniqueEntityValidatorNotYuccaModel()
    {
        $selectorManagerMock = $this->getMockBuilder(SelectorManager::class)->disableOriginalConstructor()->getMock();
        $entityManagerMock = $this->getMockBuilder(EntityManager::class)->getMock();
        $ueMock = $this->getMockBuilder(UniqueEntity::class)->disableOriginalConstructor()->getMock();
        $entityMock = new \stdClass();

        try {
            $uev = new UniqueEntityValidator($selectorManagerMock, $entityManagerMock);
            $uev->validate($entityMock, $ueMock);
            throw new \Exception('should not pass here');
        } catch (\Symfony\Component\Validator\Exception\UnexpectedTypeException $e) {
            $this->assertEquals('Expected argument of type "ModelAbstract", "stdClass" given', $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception('should not pass here');
        }
    }

    /**
     *
     */
    public function testUniqueEntityValidatorFieldsNotArrayOrString()
    {
        $selectorManagerMock = $this->getMockBuilder(SelectorManager::class)->disableOriginalConstructor()->getMock();
        $entityManagerMock = $this->getMockBuilder(EntityManager::class)->getMock();
        $ueMock = $this->getMockBuilder(UniqueEntity::class)->disableOriginalConstructor()->getMock();
        $ueMock->fields=1;
        $entityMock = $this->getMockBuilder(Properties::class)->getMock();

        try {
            $uev = new UniqueEntityValidator($selectorManagerMock, $entityManagerMock);
            $uev->validate($entityMock, $ueMock);
            throw new \Exception('should not pass here');
        } catch (\Symfony\Component\Validator\Exception\UnexpectedTypeException $e) {
            $this->assertEquals('Expected argument of type "array", "integer" given', $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception('should not pass here');
        }
    }

    /**
     *
     */
    public function testUniqueEntityValidatorWrongErrorPath()
    {
        $selectorManagerMock = $this->getMockBuilder(SelectorManager::class)->disableOriginalConstructor()->getMock();
        $entityManagerMock = $this->getMockBuilder(EntityManager::class)->getMock();
        $ueMock = $this->getMockBuilder(UniqueEntity::class)->disableOriginalConstructor()->getMock();
        $ueMock->fields='first';
        $ueMock->errorPath=1;
        $entityMock = $this->getMockBuilder(Properties::class)->getMock();

        try {
            $uev = new UniqueEntityValidator($selectorManagerMock, $entityManagerMock);
            $uev->validate($entityMock, $ueMock);
            throw new \Exception('should not pass here');
        } catch (\Symfony\Component\Validator\Exception\UnexpectedTypeException $e) {
            $this->assertEquals('Expected argument of type "string or null", "integer" given', $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception('should not pass here');
        }
    }
}
