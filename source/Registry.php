<?PHP
namespace Exceptions;

/**
 * Реестр исключений.
 *
 * Этот статический класс играет роль "глобального реестра",
 * в который попадают все исключения с флагом is_loggable,
 * и унаследованные от базового класса BaseException.
 *
 * Класс обладает возможность перегрузки хранилища исключений
 * на своё: объект с интерфейсом StorageI
 *
 * Прототип журнализатора *$save_handler* (функция обратного вызова).
 *
 * @param array             $exceptions         Хранилище исключений
 * @param callable          $reset_log          Функция сброса журнала
 * @param ArrayAccess       $logger_options     опции журналирования
 * @param ArrayAccess       $debug_options      опции для отладки (профайлинга)
 * function save_handler($exceptions, callable $reset_log, $logger_options = [], $debug_options = []);
 *
 */
class Registry
{
    /**
     * Опции для журнала запроса.
     * @var array|ArrayAccess
     */
    static public $Logger_options = [];

    /**
     * Опции для отладки
     * @var array|ArrayAccess
     */
    static public $Debug_options  = [];

    /**
     * Журнал исключений, которые нужно сохранить
     *
     * @var array
     */
    static protected $exceptions = [];

    /**
     * Обработчик, который вызывается
     * в методе save_exception_log
     *
     * @var callback
     */
    static protected $save_handler;

    /**
     * Код, вызываемый в случае необработанного исключения.
     * @var callable
     */
    static protected $unhandled_handler;

    /**
     * Код, вызываемый в случае фатального исключения
     * @var callable
     */
    static protected $fatal_handler;

    /**
     * Предыдущий обработчик ошибок
     * @var callback
     */
    static protected $old_error_handler;

    /**
     * Предыдущий обработчик исключений
     * @var callback
     */
    static protected $old_exception_handler;

    /**
     * Флаг установки глобальных обработчиков
     * @var boolen
     */
    static protected $install_global_handlers;

    /**
     * Список фатальних ошибок
     * @var array
     */
    protected static $FATAL = array
    (
        E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR
    );

    final private function __construct(){}

    /**
     * Метод регистрирует исключение в системе журналирвания
     *
     * Этот метод может быть использован в set_exception_handler()
     *
     */
    static public function register_exception($exception)
    {
        if(($exception instanceof BaseExceptionI)   === false
        && ($exception instanceof \Exception)       === false )
        {
            return;
        }

        if(is_array(self::$exceptions))
        {
            self::$exceptions[] = $exception;
        }
        elseif(self::$exceptions instanceof StorageI)
        {
            self::$exceptions->add_exception($exception);
        }
    }

    /**
     * Метод вернёт журнал исключений, которые доступны на данное время
     * Метод возвращает массив объектов.
     *
     * @return      array
     */
    static public function get_exception_log()
    {
        if(is_array(self::$exceptions))
        {
            return self::$exceptions;
        }
        elseif(self::$exceptions instanceof StorageI)
        {
            $result = self::$exceptions->get_storage();
            if(!is_array($result))
            {
                return array(new \UnexpectedValueException('StorageI->get_storage() return not array'));
            }
            else
            {
                return $result;
            }
        }
        else
        {
            return array();
        }
    }

    /**
     * Метод сбрасывает журнал исключений, если он есть.
     */
    static public function reset_exception_log()
    {
        if(self::$exceptions instanceof StorageI)
        {
            self::$exceptions->reset_storage();
        }
        else
        {
            self::$exceptions = array();
        }
    }

    /**
     * Метод заставляет явно журнализировать текущие исключения.
     */
    static public function save_exception_log()
    {
        if(is_callable(self::$save_handler))
        {
            call_user_func
            (
                self::$save_handler,
                (self::$exceptions instanceof StorageI) ? self::$exceptions->get_storage() : self::$exceptions,
                [__CLASS__, 'reset_exception_log'],
                self::$Logger_options,
                self::$Debug_options
            );
        }
    }

    /**
     * Метод устанавливает хранилище для сбора исключений.
     *
     * @param       StorageI     $storage      Хранилище
     *
     * @return      StorageI                   Метод возвращает старое хранилище, или false,
     *                                          в случае ошибки.
     */
    static public function set_registry_storage(StorageI $storage)
    {
        $old = self::$exceptions;

        self::$exceptions = $storage;

        return $old;
    }

    /**
     * Установить обработчик сохранения исключений
     *
     * @param       callable        $callback       Callback
     *
     * @return      callable                        Метод возвращает старый обработчик
     */
    static public function set_save_handler($callback)
    {
        $old = self::$save_handler;

        self::$save_handler = $callback;

        return $old;
    }

    static public function set_unhandled_handler($callback)
    {
        $old = self::$unhandled_handler;

        self::$unhandled_handler = $callback;

        return $old;
    }

    static public function set_fatal_handler($callback)
    {
        $old = self::$fatal_handler;

        self::$fatal_handler = $callback;

        return $old;
    }

    /**
     * Метод вызывает обработчика фатальных исключений
     *
     * @param       BaseExceptionI      $exception
     */
    static public function call_fatal_handler(BaseExceptionI $exception = null)
    {
        if(is_callable(self::$fatal_handler))
        {
            call_user_func(self::$fatal_handler, $exception);
        }
    }

    /**
     * Метод возвращает массив из опций (настроек) журнала.
     * Опции могут контролировать детализацию журнала,
     * прочее.
     *
     * @return      array
     */
    static public function get_logger_options()
    {
        if(is_array(self::$Logger_options) ||
        self::$Logger_options instanceof \ArrayAccess)
        {
            return self::$Logger_options;
        }
        else
        {
            return array();
        }
    }

    /**
     * Метод регистрирует все три обработчика по умолчанию
     * 1.  shutdown_function
     * 2.  error_handler
     * 3.  exception_handler
     *
     * С этого момента все ошибки и непойманные исключения
     * попадают в этот реестр
     */
    static public function install_global_handlers()
    {
        if(self::$install_global_handlers)
        {
            return;
        }

        register_shutdown_function(array(__CLASS__, 'shutdown_function'));
        self::$old_error_handler        = set_error_handler(array(__CLASS__, 'error_handler'));
        self::$old_exception_handler    = set_exception_handler(array(__CLASS__, 'exception_handler'));
        self::$install_global_handlers  = true;
    }

    /**
     * Метод восстанавливает обработчики ошибок и исключений по умолчанию
     *
     * Внимание!
     * Этот метод не может отменить обработчик shutdown_function
     */
    static public function restore_global_handlers()
    {
        if(!self::$install_global_handlers)
        {
            return;
        }

        if(!empty(self::$old_error_handler))
        {
            set_error_handler(self::$old_error_handler);
        }
        else
        {
            restore_error_handler();
        }

        if(!empty(self::$old_exception_handler))
        {
            set_exception_handler(self::$old_exception_handler);
        }
        else
        {
            restore_exception_handler();
        }
    }


    static public function exception_handler(\Exception $exception)
    {
        if($exception instanceof BaseExceptionI)
        {
            // Если исключение достигает обработчика
            // оно не логируется в случае, если:
            // - уже было журналированным
            // - или если является контейнером для другого исключения
            if($exception->is_loggable() || $exception->is_container())
            {
                new UnhandledException($exception);
                return;
            }

            $exception->set_loggable(true);
        }

        self::register_exception($exception);

        new UnhandledException($exception);

        if(is_callable(self::$unhandled_handler))
        {
            call_user_func(self::$unhandled_handler, $exception);
        }
    }

    /**
     * Метод для использования в set_error_handler
     *
     * @param        int            $errno      Класс ошибки
     * @param        string         $errstr     Описание
     * @param        string         $errfile    Файл
     * @param        string         $errline    Номер строки в файле
     * @param        array          $errcontext Контекст ошибки
     *
     * @return       boolean
    */
    static public function error_handler($errno, $errstr, $errfile, $errline, $errcontext = null)
    {
        self::register_exception
        (
            Errors\Error::create_error($errno, $errstr, $errfile, $errline, $errcontext)
        );

        /* Don't execute PHP internal error handler */
        return true;
    }

    static public function fatal_error_handler()
    {
        $error = error_get_last();
        if (!is_array($error) || !in_array($error['type'], self::$FATAL))
        {
            return;
        }
        self::error_handler($error['type'], $error['message'], $error['file'], $error['line']);
    }

    static public function shutdown_function()
    {
        self::fatal_error_handler();
        self::save_exception_log();
    }

    /**
     * Метод вернёт true, если режим отладки включен
     * для этого пространства имён.
     *
     * @param       string      $class          Имя класса или имя пространства имён
     *
     * @return      boolean
     */
    static public function is_debug($class = null)
    {
        // Если включен глобальный режим отладки - true
        if(isset(self::$Debug_options['debug']) && self::$Debug_options['debug'])
        {
            return true;
        }

        // Продолжать нет смысла,
        // Если не определён $namespace или список namespaces для отладки
        if(is_null($class) || empty(self::$Debug_options['namespaces']))
        {
            return false;
        }

        // Поиск совпадения
        foreach(self::$Debug_options['namespaces'] as $namespace)
        {
            if(strpos($class, $namespace) === 0)
            {
                return true;
            }
        }

        return false;
    }
}

?>