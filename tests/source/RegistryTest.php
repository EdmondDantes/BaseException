<?PHP
namespace Exceptions;

use \Exceptions\Errors\Error;

/**
 * Имитация PHP функции register_shutdown_function
 *
 * @param       callback      $handler
 */
function register_shutdown_function($handler)
{
    $GLOBALS['shutdown_function'] = $handler;
}

/**
 * Макет для замены error_get_last
 *
 * @return array
 */
function error_get_last()
{
    return $GLOBALS['Last_error'];
}

/**
 * Test class for Registry.
 * Generated by PHPUnit on 2012-02-12 at 19:41:28.
 */
class RegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Тестовые данные для исключения
     * @var array
     */
    protected $test_data;

    /**
     * Информация об исключении
     * @var array
     */
    protected $test_base_data;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        Registry::reset_exception_log();

        $this->test_data = array
        (
            'level'         => BaseExceptionI::CRITICAL,
            'ident'         => 'test_ident',
            'exdata'        => array(1,2,'string')
        );

        $this->test_base_data = array
        (
            'message'   => 'test message',
            'code'      => 11223344,
            'previous'  => new \Exception('previous message', 123)
        );
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        Registry::restore_global_handlers();
    }

    protected function init_Registry()
    {
        Registry::register_exception
        (
            new \Exception
            (
                'test code1',
                10
            )
        );
        Registry::register_exception
        (
            new \Exception
            (
                'test code2',
                11
            )
        );
        Registry::register_exception
        (
            new \Exception
            (
                'test code3',
                12
            )
        );
    }


    /**
     * Тестирование регистрации исключения
     */
    public function testRegister_exception()
    {
        $exceptions = array();

        new BaseException('test message1', 10);

        $exceptions[] = new LoggableException('test message 2', 10);
        $exceptions[] = new LogicalException('logical message');
        $exceptions[] = new MethodNotCallable('test_method');
        $exceptions[] = new ObjectNotInitialized($this);
        $exceptions[] = new UnexpectedMethodMode(__METHOD__, 'test_mode');
        $exceptions[] = new RecursionLimitExceeded(10);
        $exceptions[] = new RequiredValueEmpty('var', 'var_expected');
        $exceptions[] = new UnexpectedValueType('var', '112233', '11');

        foreach(Registry::get_exception_log() as $exception)
        {
            $expected = array_shift($exceptions);
            $this->assertTrue($exception === $expected);
        }

        $this->assertTrue(count($exceptions) === 0, '$exceptions contrains unknows elements');
    }

    /**
     * Эти исключения не должны регистрироваться
     */
    public function testRegister_exception_null()
    {
        Registry::register_exception(array());

        $exceptions = Registry::get_exception_log();

        $this->assertTrue(count($exceptions) === 0, '$exceptions contrains unknows elements');
    }

    /**
     * Тестирование глобальных обработчиков
     */
    public function testInstall_global_handlers()
    {
        Registry::install_global_handlers();

        // WARNING
        /** @noinspection PhpUndefinedConstantInspection */
        /** @noinspection PhpUnusedLocalVariableInspection */
        $test = CONSTANT_UNDEFINED;

        $exceptions = Registry::get_exception_log();

        $this->assertTrue(count($exceptions) === 1, '$exceptions count must equal 1');

        $this->assertArrayHasKey(0, $exceptions);

        $error = $exceptions[0];

        $this->assertInstanceOf('\Exceptions\BaseExceptionI', $error);
        $this->assertInstanceOf('\Exceptions\Errors\Error', $error);

        $this->assertEquals
        (
            "Use of undefined constant CONSTANT_UNDEFINED - assumed 'CONSTANT_UNDEFINED'",
            $error->getMessage()
        );

        $this->assertTrue(is_callable($GLOBALS['shutdown_function']));

        Registry::restore_global_handlers();
    }

    /**
     * Тестирование обработчика исключений
     */
    public function testException_handler()
    {
        // Это исключение должно залогироваться
        // в конструкторе
        $exception  = new LoggableException('test');

        // Исключение логируется как необработанное
        Registry::exception_handler(new BaseException('test2'));

        $exceptions = Registry::get_exception_log();

        $this->assertTrue(count($exceptions) === 3, '$exceptions count must equal 3');
        $this->assertTrue($exception === $exceptions[0], 'Exception not found in registry');
        $this->assertTrue($exceptions[2] instanceof UnhandledException, 'Last exception not instance of UnhandledException');

        Registry::reset_exception_log();

        // Это исключение не должно логироваться повторно
        $exception = new LoggableException('test');
        Registry::exception_handler($exception);

        $exceptions = Registry::get_exception_log();

        $this->assertTrue(count($exceptions) === 2, '$exceptions count must equal 2');
        $this->assertTrue($exception === $exceptions[0], 'Exception not found in registry');
        $this->assertTrue($exceptions[1] instanceof UnhandledException, 'Exception5 not instance of UnhandledException');

        Registry::reset_exception_log();

        // А это контейнер - он не должен логироваться сам по себе
        // зато журнализирует \Exception
        $exception = new \Exception('test');
        Registry::exception_handler(new LoggableException($exception));

        // Проверим наши предположения
        $exceptions = Registry::get_exception_log();

        $this->assertTrue(count($exceptions) === 2, '$exceptions count must equal 2');
        $this->assertTrue($exception === $exceptions[0], 'Exception not found in registry');
        $this->assertTrue($exceptions[1] instanceof UnhandledException, 'Exception5 not UnhandledException');
    }

    /**
     * Тестирование обработчика ошибок
     */
    public function testError_handler()
    {
        $errors                     =
        [
            E_ERROR                 => BaseExceptionI::ERROR,
            E_WARNING               => BaseExceptionI::WARNING,
            E_PARSE                 => BaseExceptionI::CRITICAL,
            E_NOTICE                => BaseExceptionI::NOTICE,
            E_CORE_ERROR            => BaseExceptionI::EMERGENCY,
            E_CORE_WARNING          => BaseExceptionI::WARNING,
            E_COMPILE_ERROR         => BaseExceptionI::EMERGENCY,
            E_COMPILE_WARNING       => BaseExceptionI::WARNING,
            E_USER_ERROR            => BaseExceptionI::ERROR,
            E_USER_WARNING          => BaseExceptionI::INFO,
            E_USER_NOTICE           => BaseExceptionI::DEBUG,
            E_STRICT                => BaseExceptionI::ERROR,
            E_RECOVERABLE_ERROR     => BaseExceptionI::ERROR,
            E_DEPRECATED            => BaseExceptionI::INFO,
            E_USER_DEPRECATED       => BaseExceptionI::INFO
        ];


        Registry::error_handler(E_ERROR, 'Error', __FILE__, __LINE__);
        Registry::error_handler(E_WARNING, 'Warning', __FILE__, __LINE__);
        Registry::error_handler(E_PARSE, 'Error', __FILE__, __LINE__);
        Registry::error_handler(E_NOTICE, 'Notice', __FILE__, __LINE__);
        Registry::error_handler(E_CORE_ERROR, 'Error', __FILE__, __LINE__);
        Registry::error_handler(E_CORE_WARNING, 'Warning', __FILE__, __LINE__);
        Registry::error_handler(E_COMPILE_ERROR, 'Error', __FILE__, __LINE__);
        Registry::error_handler(E_COMPILE_WARNING, 'Warning', __FILE__, __LINE__);
        Registry::error_handler(E_USER_ERROR, 'UserError', __FILE__, __LINE__);
        Registry::error_handler(E_USER_WARNING, 'UserError', __FILE__, __LINE__);
        Registry::error_handler(E_USER_NOTICE, 'UserError', __FILE__, __LINE__);
        Registry::error_handler(E_STRICT, 'Error', __FILE__, __LINE__);
        Registry::error_handler(E_RECOVERABLE_ERROR, 'Error', __FILE__, __LINE__);
        Registry::error_handler(E_DEPRECATED, 'Notice', __FILE__, __LINE__);
        Registry::error_handler(E_USER_DEPRECATED, 'Notice', __FILE__, __LINE__);

        // Проверим наши предположения
        $exceptions = Registry::get_exception_log();

        $this->assertTrue(count($exceptions) === count($errors), '$exceptions count must equal $errors');

        foreach($exceptions as $error)
        {
            list($code, $level) = each($errors);

            $this->assertInstanceOf(BaseExceptionI::class, $error);
            $this->assertInstanceOf(Error::class, $error);
            $this->assertEquals($code, $error->getCode(), '$error->getCode() failed');
            $this->assertEquals
            (
                $level,
                $error->get_level(),
                '$error->get_level() failed (line: '.$error->getLine().')'
            );
            $this->assertInstanceOf
            (
                '\Exceptions\Errors\\'.$error->getMessage(),
                $error,
                'create_error failed for: '.$error->getMessage().':'.$error->getLine()
            );
        }
    }

    public function testFatal_error_handler()
    {
        $GLOBALS['Last_error'] = array
        (
            'type'    => E_ERROR,
            'message' => 'error',
            'line'    => 11,
            'file'    => 'file'
        );

        Registry::fatal_error_handler();

        $exceptions = Registry::get_exception_log();

        $this->assertTrue(count($exceptions) === 1, '$exceptions count must equal 1');

        $this->assertArrayHasKey(0, $exceptions);

        $error = $exceptions[0];

        $this->assertInstanceOf(BaseExceptionI::class, $error);
        $this->assertInstanceOf(Error::class, $error);

        $this->assertEquals('error', $error->getMessage(), '$error->getMessage() failed');
        $this->assertEquals(11, $error->getLine(), '$error->getLine() failed');
        $this->assertEquals('file', $error->getFile(), '$error->getFile() failed');
    }

    /**
     * Журнализирование
     */
    public function testGet_exception_log()
    {
        $this->init_Registry();

        $i = 0;
        foreach(Registry::get_exception_log() as $exception)
        {
            $this->assertEquals('test code'.($i+1), $exception->getMessage(), '$exception->getMessage() failed');
            $this->assertEquals(($i+10), $exception->getCode(), '$exception->getCode() failed');

            $i++;
        }
    }

    /**
     * Обработчик несловленных исключений
     */
    public function testUnhandled_handler()
    {
        $is_call    = false;

        $exception  = new \Exception('test');

        $callback   = function($actual_exception) use($exception, &$is_call)
        {
            $this->assertTrue($actual_exception === $exception, '$actual_exception not equal $exception');
            $is_call = true;
        };

        Registry::set_unhandled_handler($callback);

        Registry::exception_handler($exception);

        $this->assertTrue($is_call, '$callback isn\'t called');
    }

    /**
     * @covers Exceptions\Registry::call_fatal_handler
     * @covers Exceptions\Registry::set_fatal_handler
     * @covers Exceptions\BaseException::is_fatal
     * @covers Exceptions\BaseException::set_fatal
     */
    public function testFatal_handler()
    {
        // Case 1: Exception-container

        $is_call    = false;

        $exception  = new \Exception('test');

        $callback   = function($actual_exception) use($exception, &$is_call)
        {
            $this->assertInstanceOf(FatalException::class, $actual_exception);

            /* @var $actual_exception \Exceptions\FatalException */

            $this->assertTrue
            (
                $actual_exception->get_previous() === $exception,
                '$actual_exception not equal $exception'
            );
            $is_call = true;
        };

        Registry::set_fatal_handler($callback);

        new FatalException($exception);

        $this->assertTrue($is_call, '$callback isn\'t called');

        // Case 2: set_fatal();
        $is_call    = false;

        $exception  = new BaseException('test');

        $callback   = function($actual_exception) use($exception, &$is_call)
        {
            $this->assertInstanceOf(BaseException::class, $actual_exception);
            $this->assertTrue
            (
                $actual_exception === $exception,
                '$actual_exception not equal $exception'
            );
            $is_call = true;
        };

        Registry::set_fatal_handler($callback);

        // call this
        $exception->set_fatal();

        $this->assertTrue($is_call, '$callback isn\'t called');
    }

    /**
     * Тестирование обработчика журнала
     */
    public function testSave_exception_handler()
    {
        $is_call    = false;

        $exception  = new LoggableException('test');

        /** @noinspection PhpUnusedParameterInspection */
        $callback   = function(array $exceptions, callable $reset, $logger_opt,  $debug_opt)
                      use($exception, &$is_call)
        {
            $this->assertTrue(is_array($exceptions), '$exceptions is not array');
            $this->assertTrue
            (
                $exceptions[0] === $exception,
                '$exceptions[0] not equal $exception'
            );
            $is_call = true;
        };

        Registry::set_save_handler($callback);

        Registry::save_exception_log();

        $this->assertTrue($is_call, '$callback isn\'t called');
    }
}