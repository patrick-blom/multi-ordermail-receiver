<?php

namespace PaBlo\MultiOrderMailReceiver\Test\Unit\Core;


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

        $this->SUT = $this->getMock(Email::class, ['__call']);
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
}
