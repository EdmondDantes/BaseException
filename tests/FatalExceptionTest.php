<?php declare(strict_types=1);

namespace IfCastle\Exceptions;

use IfCastle\Exceptions\Errors\Error;

/**
 * Test class for FatalException.
 * Generated by PHPUnit on 2012-09-08 at 11:04:54.
 */
class FatalExceptionTest            extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        // 1. Случай контейнер для исключения \Exception
        $exception = new \Exception('message', 123);

        $e = new \IfCastle\Exceptions\FatalException($exception);

        $this->assertEquals($exception->getMessage(), $e->getMessage(), '$e->getMessage() failed');
        $this->assertEquals($exception->getCode(), $e->getCode(), '$e->getCode() failed');
        $this->assertEquals($exception->getFile(), $e->getFile(), '$e->getFile() failed');
        $this->assertEquals($exception->getLine(), $e->getLine(), '$e->getLine() failed');
        $this->assertTrue(($exception === $e->getPrevious()), '$e->getPrevious() failed');
        $this->assertTrue(($exception === $e->getPreviousException()), '$e->get_previous() failed');

        $this->assertTrue($e->isFatal(), '$e->is_fatal() failed');
        $this->assertTrue($e->isLoggable(), '$e->is_loggable() failed');

        // 2. Случай контейнер для BaseExceptionI
        $exception = new \IfCastle\Exceptions\LoggableException('message', 123);

        $e = new \IfCastle\Exceptions\FatalException($exception);

        $this->assertEquals('', $e->getMessage(), '$e->getMessage() failed');
        $this->assertEquals(0, $e->getCode(), '$e->getCode() failed');
        $this->assertTrue(($exception === $e->getPrevious()), '$e->getPrevious() failed');
        $this->assertTrue(($exception === $e->getPreviousException()), '$e->get_previous() failed');

        $this->assertTrue($exception->isFatal(), '$exception->is_fatal() failed');
        $this->assertTrue($exception->isLoggable(), '$exception->is_loggable() failed');

        $this->assertFalse($e->isFatal(), '$e->is_fatal() failed');
        $this->assertTrue($e->isLoggable(), '$e->is_loggable() failed');

        // 3. Случай контейнер для BaseExceptionI, но используется класс ошибки
        $exception = new Error(1, 'message', __FILE__, __LINE__);

        $e = new \IfCastle\Exceptions\FatalException($exception);

        $this->assertEquals('', $e->getMessage(), '$e->getMessage() failed');
        $this->assertEquals(0, $e->getCode(), '$e->getCode() failed');
        $this->assertTrue(($exception === $e->getPreviousException()), '$e->get_previous() failed');

        $this->assertTrue($exception->isFatal(), '$exception->is_fatal() failed');
        $this->assertTrue($exception->isLoggable(), '$exception->is_loggable() failed');

        $this->assertFalse($e->isFatal(), '$e->is_fatal() failed');
        $this->assertTrue($e->isLoggable(), '$e->is_loggable() failed');

        // 4. Simple
        $e = new \IfCastle\Exceptions\FatalException('message');
        $e->markAsFatal();
        $this->assertTrue($e->isFatal(), '$e->is_fatal() failed');
    }
}