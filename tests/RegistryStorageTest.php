<?php

declare(strict_types=1);

namespace IfCastle\Exceptions;

use IfCastle\Exceptions\Mockups\Storage;

/**
 * A special test for testing the replacement of the Exception Registry Store.
 * 
 * Test class for Registry.
 * Generated by PHPUnit on 2012-02-12 at 19:41:28.
 */
class RegistryStorageTest extends \IfCastle\Exceptions\RegistryTest
{
    protected StorageInterface $Storage;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        \IfCastle\Exceptions\RegistryTest::setUp();

        $this->Storage = new Storage;

        \IfCastle\Exceptions\Registry::setRegistryStorage($this->Storage);
    }

    public function TestReset(): void
    {
        new \IfCastle\Exceptions\LoggableException('test message 1', 10);
        new \IfCastle\Exceptions\LoggableException('test message 2', 11);
        new \IfCastle\Exceptions\LoggableException('test message 3', 12);

        $this->assertTrue(count(Registry::getExceptionLog()) === 3, 'get_exception_log must have 3 items');
        $this->assertTrue(count($this->Storage->Exceptions) === 3, '$this->Storage->Exceptions must have 3 items');

        \IfCastle\Exceptions\Registry::resetExceptionLog();

        $this->assertTrue(count(Registry::getExceptionLog()) === 0, 'get_exception_log must have 0 items');
        $this->assertTrue(count($this->Storage->Exceptions) === 0, '$this->Storage->Exceptions must have 0 items');
    }
}