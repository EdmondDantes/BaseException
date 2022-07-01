<?php

declare(strict_types=1);

namespace Exceptions;

/**
 * Специальный тест для тестирования замены Хранилища Реестра исключений.
 * 
 * Test class for Registry.
 * Generated by PHPUnit on 2012-02-12 at 19:41:28.
 */
class RegistryStorageTest extends RegistryTest
{
    /**
     * @var \Mockups\Storage
     */
    protected $Storage;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->Storage = new \Mockups\Storage();

        Registry::setRegistryStorage($this->Storage);
    }

    public function TestReset()
    {
        new LoggableException('test message 1', 10);
        new LoggableException('test message 2', 11);
        new LoggableException('test message 3', 12);

        $this->assertTrue(count(Registry::getExceptionLog()) === 3, 'get_exception_log must have 3 items');
        $this->assertTrue(count($this->Storage->Exceptions) === 3, '$this->Storage->Exceptions must have 3 items');

        Registry::resetExceptionLog();

        $this->assertTrue(count(Registry::getExceptionLog()) === 0, 'get_exception_log must have 0 items');
        $this->assertTrue(count($this->Storage->Exceptions) === 0, '$this->Storage->Exceptions must have 0 items');
    }
}