<?php
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
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->Storage = new \Mockups\Storage();

        Registry::set_registry_storage($this->Storage);
    }

    public function TestReset()
    {
        new LoggableException('test message 1', 10);
        new LoggableException('test message 2', 11);
        new LoggableException('test message 3', 12);

        $this->assertTrue(count(Registry::get_exception_log()) === 3, 'get_exception_log must have 3 items');
        $this->assertTrue(count($this->Storage->Exceptions) === 3, '$this->Storage->Exceptions must have 3 items');

        Registry::reset_exception_log();

        $this->assertTrue(count(Registry::get_exception_log()) === 0, 'get_exception_log must have 0 items');
        $this->assertTrue(count($this->Storage->Exceptions) === 0, '$this->Storage->Exceptions must have 0 items');
    }

}

?>
