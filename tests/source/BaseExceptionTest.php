<?php declare(strict_types=1);
namespace Exceptions;

use Exceptions\Errors\Error;

class DebugException extends BaseException
{
    const DEBUG_DATA        = 'test debug data';

    public function __construct($exception, $code = 0, $previous = null)
    {
        if($code === 1)
        {
            $this->is_debug = true;
        }

        parent::__construct($exception, $code, $previous);

        $this->set_debug_data(['test' => self::DEBUG_DATA]);
    }
}

/**
 * Test class for BaseException.
 * Generated by PHPUnit on 2012-02-12 at 19:39:38.
 */
class BaseExceptionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test data for the exception
     * @var array
     */
    protected $test_data;

    /**
     * Exception info
     * @var array
     */
    protected $test_base_data;

    /**
     * Exception
     * @var BaseException
     */
    protected $BaseException;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->test_data    =
        [
            'level'         => BaseExceptionI::CRITICAL,
            'ident'         => 'test_ident',
            'exdata'        => [1, 2, 'string']
        ];

        $this->test_base_data =
        [
            'message'       => 'test message',
            'code'          => 11223344,
            'previous'      => new \Exception('previous message', 123)
        ];

        $this->BaseException = new BaseException(array_merge($this->test_data, $this->test_base_data));
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {

    }

    /**
     * @covers \Exceptions\BaseException::__construct
     * @covers \Exceptions\BaseException::setLoggable
     * @covers \Exceptions\BaseException::isLoggable
     */
    public function testConstruct()
    {
        $previous = new \Exception('ex', 2);

        $e = new BaseException('test', 10, $previous);

        $this->assertEquals('test', $e->getMessage(), '$e->getMessage() failed');
        $this->assertEquals(10, $e->getCode(), '$e->getCode() failed');
        $this->assertTrue(($previous === $e->getPrevious()), '$e->getPrevious() failed');

        $e = new BaseException($this->test_base_data);
        $this->assertEquals($this->test_base_data['message'], $e->getMessage(), '$e->getMessage() failed');
        $this->assertEquals($this->test_base_data['code'], $e->getCode(), '$e->getCode() failed');
        $this->assertTrue(($this->test_base_data['previous'] === $e->getPrevious()), '$e->getPrevious() failed');

        $e = new BaseException(array_merge($this->test_data, $this->test_base_data));
        $this->assertEquals($this->test_base_data['message'], $e->getMessage(), '$e->getMessage() failed');
        $this->assertEquals($this->test_base_data['code'], $e->getCode(), '$e->getCode() failed');
        $this->assertTrue(($this->test_base_data['previous'] === $e->getPrevious()), '$e->getPrevious() failed');
    }

    /**
     * @covers \Exceptions\BaseException::__construct
     * @covers \Exceptions\BaseException::getPreviousException
     */
    public function testConstructAsContainer()
    {
        // 1. Случай контейнер для исключения \Exception
        $exception = new \UnderflowException
        (
            $this->test_base_data['message'],
            $this->test_base_data['code']
        );

        $e = new BaseException($exception);

        $this->assertEquals($exception->getMessage(), $e->getMessage(), '$e->getMessage() failed');
        $this->assertEquals($exception->getCode(), $e->getCode(), '$e->getCode() failed');
        $this->assertEquals($exception->getFile(), $e->getFile(), '$e->getFile() failed');
        $this->assertEquals($exception->getLine(), $e->getLine(), '$e->getLine() failed');
        $this->assertTrue(($exception === $e->getPrevious()), '$e->getPrevious() failed');
        $this->assertTrue(($exception === $e->getPreviousException()), '$e->get_previous() failed');

        // 2. Случай контейнер для BaseExceptionI
        $exception = new ClassNotExist('my_class');

        $e = new BaseException($exception);

        $this->assertEquals('', $e->getMessage(), '$e->getMessage() failed');
        $this->assertEquals(0, $e->getCode(), '$e->getCode() failed');
        $this->assertTrue(($exception === $e->getPrevious()), '$e->getPrevious() failed');
        $this->assertTrue(($exception === $e->getPreviousException()), '$e->get_previous() failed');

        // 3. Случай контейнер для BaseExceptionI, но используется класс ошибки
        $exception = new Errors\Error(1, 'message', __FILE__, __LINE__);

        $e = new BaseException($exception);

        $this->assertEquals('', $e->getMessage(), '$e->getMessage() failed');
        $this->assertEquals(0, $e->getCode(), '$e->getCode() failed');
        $this->assertTrue(($exception === $e->getPreviousException()), '$e->get_previous() failed');
    }

    /**
     * @covers \Exceptions\BaseException::setLoggable
     * @covers \Exceptions\BaseException::isLoggable
     */
    public function testSetLoggable()
    {
        $this->BaseException->setLoggable(true);
        $this->assertTrue($this->BaseException->isLoggable(), 'Loggable flag must be TRUE');

        $this->BaseException->setLoggable(false);
        $this->assertFalse($this->BaseException->isLoggable(), 'Loggable flag must be FALSE');

        $this->BaseException->setLoggable(true);
        $this->assertTrue($this->BaseException->isLoggable(), 'Loggable flag must be TRUE');
    }

    /**
     * @covers \Exceptions\BaseException::getLevel
     */
    public function testGetLevel()
    {
        $this->assertEquals
        (
            $this->test_data['level'],
            $this->BaseException->getLevel(),
            'BaseException level must be BaseExceptionI::CRITICAL'
        );
    }

    /**
     * @covers \Exceptions\BaseException::getSource
     * @covers \Exceptions\BaseException::get_source_for
     */
    public function testGetSource()
    {
        $this->assertEquals(__CLASS__.'->setUp', implode('', $this->BaseException->getSource()));
        // called twice for check second call
        $this->assertEquals(__CLASS__.'->setUp', implode('', $this->BaseException->getSource()));
    }

    /**
     * @covers \Exceptions\BaseException::getExceptionData
     */
    public function testGetData()
    {
        $data = $this->BaseException->getExceptionData();

        $this->assertTrue(is_array($data), 'data must be array');

        foreach($this->test_data as $key => $value)
        {
            $this->assertArrayHasKey($key, $data);
            $this->assertEquals($value, $data[$key]);
            unset($data[$key]);
        }

        $this->assertTrue(count($data) === 0, 'Data has contain unknown elements');
    }

    public function testToArray()
    {
        $data           = $this->BaseException->toArray();

        $mockup         = array
        (
            'type'      => BaseException::class,
            'source'    => ['source' => get_class($this), 'type' => '->', 'function' => 'setUp'],
            'message'   => $this->test_base_data['message'],
            'template'  => '',
            'code'      => $this->test_base_data['code'],
            'data'      => ''
        );

        $this->assertTrue(is_array($data), 'data must be array');

        foreach($mockup as $main_key => $main_value)
        {
            $this->assertArrayHasKey($main_key, $data);

            if('data' === $main_key)
            {
                foreach($this->test_data as $key => $value)
                {
                    $this->assertArrayHasKey($key, $data['data']);
                    $this->assertEquals($value, $data['data'][$key]);
                }
            }
            elseif('source' === $main_key)
            {
                $this->assertEquals(serialize($main_value), serialize($data[$main_key]));
            }
            else
            {
                $this->assertEquals($main_value, $data[$main_key]);
            }
            unset($data[$main_key]);
        }

        $this->assertTrue(count($data) === 0, 'Data has contain unknown elements');
    }

    /**
     * Тест to_array для исключения-контейнера
     */
    public function testToArrayForContainer()
    {
        $exception      = new LoggableException(new \Exception('test', 2));

        $data           = $exception->toArray();

        $mockup = array
        (
            'type'      => \Exception::class,
            'source'    => ['source' => get_class($this), 'type' => '->', 'function' => 'testToArrayForContainer'],
            'message'   => 'test',
            'code'      => 2,
            'data'      => [],
            'container' => LoggableException::class
        );

        $this->assertTrue(is_array($data), 'data must be array');

        foreach($mockup as $main_key => $main_value)
        {
            $this->assertArrayHasKey($main_key, $data);
            $this->assertEquals($mockup[$main_key], $main_value, "$main_key is not match");

            unset($data[$main_key]);
        }

        $this->assertTrue(count($data) === 0, 'Data has contain unknown elements');
    }

    /**
     * Тест to_array для исключения-контейнера
     */
    public function testToArrayForContainer2()
    {
        $exception      = new LoggableException(new BaseException(['message' => 'test', 'exdata' => 'data']));

        $data           = $exception->toArray();

        $mockup         =
        [
            'type'      => BaseException::class,
            'source'    => ['source' => get_class($this), 'type' => '->', 'function' => 'testToArrayForContainer2'],
            'message'   => 'test',
            'template'  => '',
            'code'      => 0,
            'data'      => ['exdata' => $data],
            'container' => LoggableException::class
        ];

        $this->assertTrue(is_array($data), 'data must be array');

        foreach($mockup as $main_key => $main_value)
        {
            $this->assertArrayHasKey($main_key, $data);
            $this->assertEquals($mockup[$main_key], $main_value);

            unset($data[$main_key]);
        }

        $this->assertTrue(count($data) === 0, 'Data has contain unknown elements');
    }

    public function testToArrayForTemplate()
    {
        $test           = new \ArrayObject([1, 2, 3]);

        $exception      = new UnexpectedValueType('$test', $test, 'string');

        $data           = $exception->toArray();

        $mockup         = array
        (
            'type'      => 'Exceptions\UnexpectedValueType',
            'source'    => ['source' => get_class($this), 'type' => '->', 'function' => 'testToArrayForTemplate'],
            'message'   => '',
            'template'  => 'Unexpected type occurred for the value {name} and type {type}. Expected {expected}',
            'code'      => 0
        );

        $this->assertTrue(is_array($data), 'data must be array');

        foreach($mockup as $main_key => $main_value)
        {
            $this->assertArrayHasKey($main_key, $data);

            $this->assertEquals($mockup[$main_key], $data[$main_key], "$main_key is failed");
        }
    }

    public function testLoggableContainer()
    {
        Registry::resetExceptionLog();

        $not_loggable_exception     = new BaseException('no logged message');

        new LoggableException($not_loggable_exception);

        // $exception is container for BaseException
        // and BaseException must be logged too.

        $exceptions                 = Registry::getExceptionLog();

        $this->assertTrue(count($exceptions) === 1, 'count($exceptions) !== 1');
        $this->assertTrue($not_loggable_exception === $exceptions[0], '$not_loggable_exception is failed');
    }

    public function testGetPrevious()
    {
        // 1.
        $exception                  = new BaseException('message');

        $previous                   = $exception->getPreviousException();

        $this->assertEquals(null, $previous);

        // 2.
        $previous                   = new \Exception('previous');

        $exception                  = new BaseException('message', 0, $previous);

        $this->assertTrue($previous === $exception->getPreviousException(), '$previous failed');

        // 3.
        $previous                   = new Error(Error::ERROR, 'test error', __FILE__, __LINE__);

        $exception                  = new BaseException(['message' => 'test', 'previous' => $previous]);

        $this->assertTrue($previous === $exception->getPreviousException(), '$previous failed for new Error');
    }

    public function testDebugData()
    {
        $exception                  = new DebugException('message');

        $this->assertEquals([], $exception->getDebugData());

        $exception                  = new DebugException('message', 1);

        $this->assertEquals(['test' => DebugException::DEBUG_DATA], $exception->getDebugData());

        Registry::$DebugOptions['debug'] = true;

        $exception                  = new DebugException('message');

        $this->assertEquals(['test' => DebugException::DEBUG_DATA], $exception->getDebugData());
    }

    public function testAppendData()
    {
        $exception                  = new BaseException(['data' => 'test']);

        $exception->appendData(['append_data' => 'data']);

        $this->assertEquals(['data' => 'test', ['append_data' => 'data']], $exception->getExceptionData());
    }
}