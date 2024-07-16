<?php declare(strict_types=1);
namespace IfCastle\Exceptions;

/**
 * Register of exceptions.
 *
 * This is a static class which used as global registry for exceptions.
 * It defines the internal storage for exceptions which can be redefined by a programmer.
 *
 * Really this class not log an exception.
 * It's stores them until called $save_handler.
 */
class Registry
{
    /**
     * Options for logger
     * @var array|\ArrayAccess
     */
    static public array $LoggerOptions = [];

    /**
     * Options for debug mode
     * @var array|\ArrayAccess
     */
    static public array $DebugOptions  = [];

    /**
     * List of exception
     *
     * @var BaseException[]|\Exception[]|StorageInterface
     */
    static protected array|StorageInterface $exceptions = [];

    /**
     * Handler which called from save_exception_log
     */
    static protected ?SaveHandlerI $saveHandler = null;

    /**
     * Handler for unhandled exception
     */
    static protected ?HandlerInterface $unhandledHandler = null;

    /**
     * Handler called for fatal exception
     */
    static protected ?HandlerInterface $fatalHandler = null;

    /**
     * Old error handler
     * @var callback
     */
    static protected $oldErrorHandler;

    /**
     * Old exception handler
     * @var callback
     */
    static protected $oldExceptionHandler;

    /**
     * Setup global handler flag
     */
    static protected bool $installGlobalHandlers = false;

    /**
     * List of fatal php error
     */
    protected static array $FATAL = [\E_ERROR, \E_PARSE, \E_CORE_ERROR, \E_COMPILE_ERROR];

    final private function __construct(){}

    /**
     * Registered exception.
     *
     * This method may be used with set_exception_handler()
     *
     * @param BaseExceptionInterface|\Throwable $exception
     *
     */
    static public function registerException(mixed $exception): void
    {
        if(!($exception instanceof \Throwable || $exception instanceof BaseExceptionInterface)) {
            return;
        }
        
        if(is_array(self::$exceptions))
        {
            self::$exceptions[] = $exception;
        }
        elseif(self::$exceptions instanceof StorageInterface)
        {
            self::$exceptions->addException($exception);
        }
    }

    /**
     * Returns the list of exception
     *
     * @return      BaseException[]|\Exception[]
     */
    static public function getExceptionLog(): array
    {
        if (is_array(self::$exceptions)) {
            return self::$exceptions;
        }
        if (self::$exceptions instanceof StorageInterface) {
            $result = self::$exceptions->getStorageExceptions();
            if(!is_array($result))
            {
                return [new \UnexpectedValueException('StorageI->get_storage() return not array')];
            }
            return $result;
        }
        else
        {
            return [];
        }
    }

    /**
     * Resets exception storage
     */
    static public function resetExceptionLog(): void
    {
        if(self::$exceptions instanceof StorageInterface)
        {
            self::$exceptions->resetStorage();
        }
        else
        {
            self::$exceptions = [];
        }
    }

    /**
     * Saves registry exceptions to log.
     */
    static public function saveExceptionLog(): void
    {
        if(self::$saveHandler instanceof SaveHandlerI)
        {
            self::$saveHandler->saveExceptions
            (
                (self::$exceptions instanceof StorageInterface) ? self::$exceptions->getStorageExceptions() : self::$exceptions,
                self::resetExceptionLog(...),
                self::$LoggerOptions,
                self::$DebugOptions
            );
        }
    }

    /**
     * Setup custom storage for exceptions
     *
     * @param       StorageInterface $storage Custom storage
     *
     * @return      ?StorageInterface                   returns older storage if exists
     */
    static public function setRegistryStorage(StorageInterface $storage): ?StorageInterface
    {
        $old = self::$exceptions;

        self::$exceptions = $storage;

        return $old instanceof StorageInterface ? $old : null;
    }

    /**
     * Setup save handler
     *
     * @param       ?SaveHandlerI                $handler       Handler
     *
     * @return      SaveHandlerI|null           Returns old handler if exists
     */
    static public function setSaveHandler(SaveHandlerI $handler = null): ?SaveHandlerI
    {
        $old = self::$saveHandler;

        self::$saveHandler = $handler;

        return $old;
    }

    /**
     * @param       ?HandlerInterface $handler
     */
    static public function setUnhandledHandler(HandlerInterface $handler = null): ?HandlerInterface
    {
        $old                        = self::$unhandledHandler;

        self::$unhandledHandler     = $handler;

        return $old;
    }

    /**
     * @param       HandlerInterface|null $handler
     */
    static public function setFatalHandler(HandlerInterface $handler = null): HandlerInterface|null
    {
        $old                        = self::$fatalHandler;

        self::$fatalHandler         = $handler;

        return $old;
    }

    /**
     * Invokes the handler if there is
     *
     * @param       ?BaseExceptionInterface $exception
     */
    static public function callFatalHandler(BaseExceptionInterface $exception = null): void
    {
        if(self::$fatalHandler instanceof HandlerInterface)
        {
            self::$fatalHandler->exceptionHandler($exception);
        }
    }

    /**
     * Return list of logger options
     */
    static public function getLoggerOptions(): array
    {
        if(is_array(self::$LoggerOptions) ||
        self::$LoggerOptions instanceof \ArrayAccess)
        {
            return self::$LoggerOptions;
        }
        return [];
    }

    /**
     * Registers three default handlers:
     *
     * 1.  shutdown_function
     * 2.  error_handler
     * 3.  exception_handler
     *
     */
    static public function installGlobalHandlers(): void
    {
        if(self::$installGlobalHandlers)
        {
            return;
        }

        register_shutdown_function(self::shutdownFunction(...));
        self::$oldErrorHandler        = set_error_handler(self::errorHandler(...));
        self::$oldExceptionHandler    = set_exception_handler(self::exceptionHandler(...));
        self::$installGlobalHandlers  = true;
    }

    /**
     * Restores default handlers.
     *
     */
    static public function restoreGlobalHandlers(): void
    {
        if(!self::$installGlobalHandlers)
        {
            return;
        }

        self::$installGlobalHandlers  = false;

        if(!empty(self::$oldErrorHandler))
        {
            set_error_handler(self::$oldErrorHandler);
        }
        else
        {
            restore_error_handler();
        }

        if(!empty(self::$oldExceptionHandler))
        {
            set_exception_handler(self::$oldExceptionHandler);
        }
        else
        {
            restore_exception_handler();
        }
    }


    static public function exceptionHandler(\Throwable $exception): void
    {
        if($exception instanceof BaseExceptionInterface === false) {
            self::registerException($exception);
        } else if(!($exception->isLoggable() || $exception->isContainer())) {
            // When exception reaches this handler
            // its not logged if:
            // - already was logged
            // - or is container
            $exception->setLoggable(true);
            self::registerException($exception);
        }
        
        new UnhandledException($exception);
        
        if(self::$unhandledHandler instanceof HandlerInterface)
        {
            self::$unhandledHandler->exceptionHandler($exception);
        }
    }
    
    /**
     * The method for set_error_handler
     *
     *
     */
    static public function errorHandler(int $code, string $message, string $file, int|string $line): bool
    {
        self::registerException
        (
            Errors\Error::createError($code, $message, $file, (int)$line)
        );

        /* Don't execute PHP internal error handler */
        return true;
    }

    static public function fatalErrorHandler(): void
    {
        $error                      = error_get_last();
        
        if (!is_array($error) || !in_array($error['type'], self::$FATAL))
        {
            return;
        }
        
        self::errorHandler($error['type'], $error['message'], $error['file'], $error['line']);
    }

    static public function shutdownFunction(): void
    {
        self::fatalErrorHandler();
        self::saveExceptionLog();
    }

    /**
     * Returns true if debug mode was enabled
     *
     * @param       ?string      $class          name of class or namespace
     */
    static public function isDebug(string $class = null): bool
    {
        // If global debug mode on - return true.
        if(isset(self::$DebugOptions['debug']) && self::$DebugOptions['debug'])
        {
            return true;
        }

        // if namespaces not defined - return
        if(is_null($class) || empty(self::$DebugOptions['namespaces']))
        {
            return false;
        }

        // Searching for matches
        foreach(self::$DebugOptions['namespaces'] as $namespace)
        {
            if(str_starts_with($class, (string) $namespace))
            {
                return true;
            }
        }

        return false;
    }
}