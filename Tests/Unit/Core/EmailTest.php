<?php

namespace PaBlo\MultiOrderMailReceiver\Test\Unit\Core;


use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\TestingLibrary\UnitTestCase;
use PaBlo\MultiOrderMailReceiver\Core\Email;

/**
 * Class EmailTest
 * UNIT/INTEGRATION tests for core class Email.
 *
 * @package PaBlo\MultiOrderMailReceiver\Test\Unit\Core
 */
class EmailTest extends UnitTestCase
{
    /**
     * Subject under the test.
     *
     * @var Email
     */
    protected $SUT;

    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMockBuilder(Email::class)
            ->setMethods(['__call'])
            ->getMock();
    }

    /**
     * @covers \PaBlo\MultiOrderMailReceiver\Core\Email::getCarbonCopy
     */
    public function testGetCarbonCopy_nothingSet_returnEmptyArray(): void
    {
        $this->assertEmpty($this->SUT->getCarbonCopy());
        $this->assertCount(0, $this->SUT->getCarbonCopy());
    }

    /**
     * @covers \PaBlo\MultiOrderMailReceiver\Core\Email::getCarbonCopy
     */
    public function testGetCarbonCopy_dataSet_returnArray(): void
    {
        $this->setProtectedClassProperty($this->SUT, '_aCarbonCopies', [['foo@bar.de', 'FooBar']]);

        $this->assertNotEmpty($this->SUT->getCarbonCopy());
        $this->assertCount(1, $this->SUT->getCarbonCopy());
    }

    /**
     * @covers \PaBlo\MultiOrderMailReceiver\Core\Email::clearAllCarbonCopies
     */
    public function testClearAllCarbonCopies_willEmptyInternalProperty(): void
    {
        $this->setProtectedClassProperty($this->SUT, '_aCarbonCopies', [['foo@bar.de', 'FooBar']]);

        $this->SUT->clearAllCarbonCopies();

        $this->assertEmpty($this->SUT->getCarbonCopy());
        $this->assertCount(0, $this->SUT->getCarbonCopy());
    }

    /**
     * @covers \PaBlo\MultiOrderMailReceiver\Core\Email::setCarbonCopy
     * @covers \PaBlo\MultiOrderMailReceiver\Core\Email::idnToAscii
     */
    public function testSetCarbonCopy_withValidData(): void
    {
        $this->SUT->setCarbonCopy('max@my-mail.com', 'Max Muster');

        $carbonCopies = $this->getProtectedClassProperty($this->SUT, '_aCarbonCopies');

        $this->assertNotEmpty($carbonCopies);
        $this->assertCount(1, $carbonCopies);

        $this->assertSame('max@my-mail.com', $carbonCopies[0][0]);
        $this->assertSame('Max Muster', $carbonCopies[0][1]);
    }


    /**
     * @covers \PaBlo\MultiOrderMailReceiver\Core\Email::setCarbonCopy
     * @covers \PaBlo\MultiOrderMailReceiver\Core\Email::idnToAscii
     */
    public function testSetCarbonCopy_withNonValidData(): void
    {
        $this->expectException(\PHPMailer\PHPMailer\Exception::class);

        $this->SUT->setCarbonCopy('This is no mail address', 'mail address');

        $carbonCopies = $this->getProtectedClassProperty($this->SUT, '_aCarbonCopies');

        $this->assertEmpty($carbonCopies);
        $this->assertCount(0, $carbonCopies);
    }

    /**
     * @covers \PaBlo\MultiOrderMailReceiver\Core\Email::setCarbonCopyActive
     */
    public function testSetCarbonCopyActive_withInternalDefaultStateFalse(): void
    {
        $internalState = $this->getProtectedClassProperty($this->SUT, '_blCarbonCopyActiveState');
        $this->assertFalse($internalState);
    }

    /**
     * @covers \PaBlo\MultiOrderMailReceiver\Core\Email::setCarbonCopyActive
     * @throws \ReflectionException
     */
    public function testSetCarbonCopyActive_willSetInternalStateTrue(): void
    {
        $reflectedMethod = new \ReflectionMethod(Email::class, 'setCarbonCopyActive');
        $reflectedMethod->setAccessible(true);

        $reflectedMethod->invoke($this->SUT);

        $internalState = $this->getProtectedClassProperty($this->SUT, '_blCarbonCopyActiveState');
        $this->assertTrue($internalState);
    }


    /**
     * @covers \PaBlo\MultiOrderMailReceiver\Core\Email::getCarbonCopyActiveState
     */
    public function testGetCarbonCopyActive_willGetInternalStateFalse(): void
    {
        $reflectedGetter = new \ReflectionMethod(Email::class, 'getCarbonCopyActiveState');
        $reflectedGetter->setAccessible(true);

        $internalState = $reflectedGetter->invoke($this->SUT);
        $this->assertFalse($internalState);
    }

    /**
     * @covers \PaBlo\MultiOrderMailReceiver\Core\Email::getCarbonCopyActiveState
     * @throws \ReflectionException
     */
    public function testGetCarbonCopyActive_willGetInternalStateTrue(): void
    {
        $reflectedSetter = new \ReflectionMethod(Email::class, 'setCarbonCopyActive');
        $reflectedSetter->setAccessible(true);

        $reflectedGetter = new \ReflectionMethod(Email::class, 'getCarbonCopyActiveState');
        $reflectedGetter->setAccessible(true);

        $reflectedSetter->invoke($this->SUT);

        $internalState = $reflectedGetter->invoke($this->SUT);
        $this->assertTrue($internalState);
    }

    /**
     * @covers \PaBlo\MultiOrderMailReceiver\Core\Email::idnToAscii
     * @throws \ReflectionException
     */
    public function testIdnToAscii_willReturnNormalString(): void
    {
        $reflectedMethod = new \ReflectionMethod(Email::class, 'idnToAscii');
        $reflectedMethod->setAccessible(true);

        $result = $reflectedMethod->invokeArgs($this->SUT, ['täst.de']);
        $this->assertSame('xn--tst-qla.de', $result);
    }
}
