<?php

declare(strict_types=1);

namespace Exceptions;

/**
 * The Base class for exception handling.
 *
 * <h1>Differences from the \Exception class</h1>
 * <ul>
 * <li>An exception may be a parameter array, with the keys defined by the programmer.</li>
 * <li>An exception may be a container for another.</li>
 * <li>An exception may be logged.</li>
 * <li>An exception may have different aspects (fatal, runtime, system).</li>
 * </ul>
 *
 * <h1>Logger management</h1>
 *
 * The exception is logged into the constructor.
 * In reality, it is stored in the `Registry`, and then just logged.
 *
 * Methods to control the logging:
 * - is_loggable();
 * - set_loggable($new_flag);
 *
 * BaseException not logged by default.
 * But you can easily override this by using the property `is_logged`
 * (for example in the child class).
 *
 * <h1>Exception-container</h1>
 *
 * Exception-container can hide another type of exception.
 *
 * <h1>Support the template</h1>
 *
 * Class supports the template of the error message instead of the plain message.
 *
 */
class BaseException                 extends     \Exception
                                    implements  BaseExceptionI
{
    public static function serializeToArray(\Throwable $throwable = null): array
    {
        if($throwable instanceof BaseException) {
            return $throwable->toArray();
        } else {
            return [
                'message'           => $throwable->getMessage(),
                'code'              => $throwable->getCode(),
                'file'              => $throwable->getFile(),
                'line'              => $throwable->getLine(),
                'trace'             => $throwable->getTrace()
            ];
        }
    }
    
    use HelperT;
    use ArraySerializerT;
    use TemplateHandlerT;

    /**
     * Layout of the default properties
     * @var array
     */
    static protected array $baseProps = ['message' => '', 'code' => 0, 'previous' => null, 'template' => '', 'tags' => []];

    /**
     * template message
     * @var string
     */
    protected string $template      = '';

    /**
     * Extra data to exception
     * @var         array
     */
    protected array $data           = [];
    
    /**
     * Tags for logging
     * @var string[]
     */
    protected array $tags           = [];

    /**
     * Source of error
     * @var         array|null
     */
    protected ?array $source        = null;

    /**
     * Debug data
     * @var         array
     */
    protected array $debugData      = [];

    /**
     * Container flag
     * @var         boolean
     */
    protected bool $isContainer    = false;

    /**
     * Logged flag.
     * If it's equal true,
     * is going to be recorded in the journal.
     *
     * @var         boolean
     */
    protected bool $isLoggable      = false;

    /**
     * Fatal exception flag
     *
     * @var         boolean
     */
    protected bool $isFatal         = false;

    /**
     * Debug mode flag
     * @var         boolean|null
     */
    protected ?bool $isDebug        = null;
    
    /**
     * BaseException constructor.
     *
     * Use Cases:
     *
     * 1. $exception - string. In this case, the parameters involved: $code, $previous.
     *    and $exception is the $message.
     * 2. $exception - array.
     *    Key of array: message, code, previous
     *    overridden the parameters $code and $previous,
     *    and other data is saved to the property `$data`.
     * 3. $exception - BaseExceptionI
     *    In this case, exception acts as container,
     *    but it does not inherit a data from the $exception.
     * 4. $exception - \Throwable -
     *    In this case, exception acts as container,
     *    and inherits a data from the $exception
     *
     * @param BaseExceptionI|\Throwable|array|string $exception    Exception data
     * @param int                         $code         Code
     * @param \Throwable|null             $previous     Previous or aggregate exception
     */
    public function __construct(BaseExceptionI|\Throwable|array|string $exception, int $code = 0, \Throwable $previous = null)
    {
        $template               = '';
        $message                = '';
        $tags                   = [];

        if($exception instanceof BaseExceptionI)
        {
            $this->isContainer = true;

            // If aggregate $exception wasn't journaled,
            // and this is going to be to journal,
            // then an $exception should be registered.
            if(!$exception->isLoggable() && $this->isLoggable)
            {
                Registry::registerException($exception);
            }

            $previous           = $exception;
        }
        elseif($exception instanceof \Throwable)
        {
            $this->isContainer  = true;

            // Inherit properties from the aggregated $exception
            $this->file         = $exception->getFile();
            $this->line         = $exception->getLine();

            $message            = $exception->getMessage();
            $code               = $exception->getCode();
            $previous           = $exception;

            if($this->isLoggable)
            {
                Registry::registerException($exception);
            }
        }
        elseif(is_array($exception))
        {
            // The code separating the parameters on the basic and additional
            $baseProps          = array_intersect_key($exception, self::$baseProps);
            $this->data         = array_diff_key($exception, $baseProps);
            extract($baseProps);
        }
        else
        {
            $message            = (string)$exception;
        }

        // handle template message
        if(empty($template) && !empty($this->template))
        {
            $template           = $this->template;
        }

        $this->tags             += $tags;
        
        if(!empty($template))
        {
            $this->template     = $template;

            // override message key if not exists
            if(!empty($message))
            {
                $this->data['message'] = $message;
            }

            $message            = $this->handleTemplate($this->template, $this->data, $message, $code, $previous);
        }

        // parent construct
        if( $previous instanceof BaseExceptionI
        && ($previous instanceof \Throwable) === false)
        {
            parent::__construct($message, $code);

            $this->data['previous'] = $previous;
        }
        else
        {
            parent::__construct($message, $code, $previous);
        }

        // The container is never in the journal
        if($this->isLoggable && $this->isContainer === false)
        {
            Registry::registerException($this);
        }

        // The handler for fatal exceptions
        if($this->isFatal)
        {
            Registry::callFatalHandler($this);
        }
    }

    /**
     * Returns template message
     *
     * @return      string
     */
    public function template(): string
    {
        return $this->template;
    }
    
    public function getTags(): array
    {
        return $this->tags;
    }
    
    /**
     * The method returns a logging flag.
     *
     * TRUE - indicates that an exception is going to be written to the log.
     *
     * @return boolean
     */
    public function isLoggable(): bool
    {
        return $this->isLoggable;
    }
    
    /**
     * The method sets a logging flag.
     *
     * If set flag from TRUE to FALSE,
     * then the exception will not be saved to log (maybe).
     *
     * @param boolean $flag logging flag
     *
     * @return  $this
     */
    public function setLoggable(bool $flag): static
    {
        $this->isLoggable           = $flag;

        return $this;
    }

    /**
     * The method returns TRUE - if an exception is fatal.
     *
     * @return boolean
     */
    public function isFatal(): bool
    {
        return $this->isFatal;
    }

    /**
     * Method marks the exception as fatal.
     *
     * Calling this method may lead to a call handler fatal errors.
     *
     * @return  BaseException
     */
    public function markAsFatal(): static
    {
        if(!$this->isFatal)
        {
            $this->isFatal     = true;
            Registry::callFatalHandler($this);
            return $this;
        }

        $this->isFatal         = true;

        return $this;
    }

    /**
     * The method will return true, if an exception is the container.
     * @return boolean
     */
    public function isContainer(): bool
    {
        return $this->isContainer;
    }

    /**
     * The method returns an error level
     * @return      int
     */
    public function getLevel(): int
    {
        return empty($this->data['level']) ? self::ERROR : $this->data['level'];
    }

    /**
     * The method returns the source of error.
     *
     * The method returns an array of values:
     * [
     *      'source'    => class name or file name, where the exception occurred
     *      'type'      => type of the call
     *      'function'  => function or method or closure
     * ]
     *
     * @return array|null
     */
    public function getSource(): ?array
    {
        if(is_array($this->source))
        {
            return $this->source;
        }

        return $this->source    = $this->getSourceFor($this);
    }

    /**
     * The method returns previous exception.
     *
     * It extends the method Exception::getPrevious,
     * and it allows to work with objects which not inherited from Exception class,
     * but they are instances of BaseExceptionI.
     *
     * Also, if this exception is container, when that method may be used
     * for getting contained object of BaseExceptionI.
     *
     * @return      BaseExceptionI|\Throwable|null
     */
    public function getPreviousException(): BaseExceptionI|\Throwable|null
    {
        $previous       = $this->getPrevious();

        if($previous instanceof \Throwable)
        {
            return $previous;
        }
        elseif(isset($this->data['previous'])
        && $this->data['previous'] instanceof BaseExceptionI)
        {
            return $this->data['previous'];
        }

        return null;
    }

    /**
     * The method returns extra data for exception
     * @return array
     */
    public function getExceptionData(): array
    {
        return $this->data;
    }

    public function appendData(array $data): static
    {
        $this->data[]       = $data;

        return $this;
    }

    /**
     * The method returns debug data for exception
     *
     * @return array
     */
    public function getDebugData(): array
    {
        return $this->debugData;
    }

    /**
     * The method serialized object to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        // If this exception is container, then it returns data of container.
        if($this->isContainer())
        {
            $previous       = $this->getPreviousException();

            if($previous instanceof BaseExceptionI)
            {
                $res        = $previous->toArray();
            }
            else
            {
                $res =
                [
                    'type'      => get_class($previous),
                    'source'    => $this->getSourceFor($previous),
                    'message'   => $previous->getMessage(),
                    'code'      => $previous->getCode(),
                    'data'      => []
                ];
            }

            if(empty($res['container']))
            {
                $res['container'] = get_class($this);
            }

            return $res;
        }

        // override the exception message if the template was defined
        if($this->template() !== '')
        {

            $message    = $this->getExceptionData()['message'] ?? '';
        }
        else
        {
            $message    = $this->getMessage();
        }

        return
        [
            'type'      => get_class($this),
            'source'    => $this->getSource(),
            'message'   => $message,
            'template'  => $this->template(),
            'tags'      => $this->getTags(),
            'code'      => $this->getCode(),
            'data'      => $this->getExceptionData()
        ];
    }

    /**
     * Returns information about value type
     *
     * @param   mixed           $value      Value
     *
     * @return  string|array
     */
    protected function typeInfo(mixed $value): string|array
    {
        return $this->getValueType($value);
    }

    /**
     * The method returns true if debug mode is on.
     *
     * @return      boolean
     */
    protected function isDebug(): bool
    {
        if(is_bool($this->isDebug))
        {
            return $this->isDebug;
        }

        return $this->isDebug = Registry::isDebug(get_class($this));
    }

    /**
     * The method saved debug data if debug mode is active.
     *
     * @param       array       $data           debug data
     * @return      $this
     */
    protected function setDebugData(array $data): static
    {
        if(!$this->isDebug())
        {
            return $this;
        }

        $this->debugData   = $data;

        return $this;
    }
}