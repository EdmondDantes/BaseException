<?php declare(strict_types=1);

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
    use HelperT;
    use ArraySerializerT;
    use TemplateHandlerT;

    /**
     * Layout of the default properties
     * @var array
     */
    static protected array $base_props = ['message' => '', 'code' => 0, 'previous' => null, 'template' => ''];

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
     * Source of error
     * @var         array|null
     */
    protected ?array $source        = null;

    /**
     * Debug data
     * @var         array
     */
    protected array $debug_data     = [];

    /**
     * Container flag
     * @var         boolean
     */
    protected bool $is_container    = false;

    /**
     * Logged flag.
     * If it's equal true,
     * is going to be recorded in the journal.
     *
     * @var         boolean
     */
    protected bool $is_loggable     = false;

    /**
     * Fatal exception flag
     *
     * @var         boolean
     */
    protected bool $is_fatal        = false;

    /**
     * Debug mode flag
     * @var         boolean|null
     */
    protected ?bool $is_debug        = null;
    
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
     * @param mixed $exception Exception
     * @param int   $code      Code
     * @param null  $previous  Previous or aggregate exception
     */
    public function __construct(mixed $exception, $code = 0, $previous = null)
    {
        $template               = '';
        $message                = '';

        if($exception instanceof BaseExceptionI)
        {
            $this->is_container = true;

            // If aggregate $exception wasn't journaled,
            // and this is going to be to journal,
            // then an $exception should be registered.
            if(!$exception->is_loggable() && $this->is_loggable)
            {
                Registry::register_exception($exception);
            }

            $previous           = $exception;
        }
        elseif($exception instanceof \Throwable)
        {
            $this->is_container = true;

            // Inherit properties from the aggregated $exception
            $this->file         = $exception->getFile();
            $this->line         = $exception->getLine();

            $message            = $exception->getMessage();
            $code               = $exception->getCode();
            $previous           = $exception;

            if($this->is_loggable)
            {
                Registry::register_exception($exception);
            }
        }
        elseif(is_array($exception))
        {
            // The code separating the parameters on the basic and additional
            $base_props         = array_intersect_key($exception, self::$base_props);
            $this->data         = array_diff_key($exception, $base_props);
            extract($base_props);
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

        if(!empty($template))
        {
            $this->template     = $template;

            // override message key if not exists
            if(!empty($message))
            {
                $this->data['message'] = $message;
            }

            $message            = $this->handle_template($this->template, $this->data, $message, $code, $previous);
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
        if($this->is_loggable && $this->is_container === false)
        {
            Registry::register_exception($this);
        }

        // The handler for fatal exceptions
        if($this->is_fatal)
        {
            Registry::call_fatal_handler($this);
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

    /**
     * The method returns a logging flag.
     *
     * TRUE - indicates that an exception is going to be written to the log.
     *
     * @return boolean
     */
    public function is_loggable(): bool
    {
        return $this->is_loggable;
    }
    
    /**
     * The method sets a logging flag.
     *
     * If set flag from TRUE to FALSE,
     * then the exception will not be saved to log (may be).
     *
     * @param boolean $flag logging flag
     *
     * @return  $this
     */
    public function set_loggable(bool $flag)
    {
        $this->is_loggable = (boolean) $flag;

        return $this;
    }

    /**
     * The method returns TRUE - if an exception is fatal.
     *
     * @return boolean
     */
    public function is_fatal(): bool
    {
        return $this->is_fatal;
    }

    /**
     * Method marks the exception as fatal.
     *
     * Calling this method may lead to a call handler fatal errors.
     *
     * @return  BaseException
     */
    public function set_fatal()
    {
        if(!$this->is_fatal)
        {
            $this->is_fatal     = true;
            Registry::call_fatal_handler($this);
            return $this;
        }

        $this->is_fatal         = true;

        return $this;
    }

    /**
     * The method will return true, if an exception is the container.
     * @return boolean
     */
    public function is_container(): bool
    {
        return $this->is_container;
    }

    /**
     * The method returns an error level
     * @return      int
     */
    public function get_level(): int
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
    public function get_source(): ?array
    {
        if(is_array($this->source))
        {
            return $this->source;
        }

        return $this->source    = $this->get_source_for($this);
    }

    /**
     * The method returns previous exception.
     *
     * It extends the method Exception::getPrevious,
     * and it allows to work with objects which not inherited from Exception class,
     * but they are instances of BaseExceptionI.
     *
     * Also if this exception is container, when that method may be used
     * for getting contained object of BaseExceptionI.
     *
     * @return      BaseExceptionI|\Throwable|null
     */
    public function get_previous(): BaseExceptionI|\Throwable|null
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
    public function get_data(): array
    {
        return $this->data;
    }

    public function append_data(array $data)
    {
        $this->data[]       = $data;

        return $this;
    }

    /**
     * The method returns debug data for exception
     *
     * @return array
     */
    public function get_debug_data(): array
    {
        return $this->debug_data;
    }

    /**
     * The method serialized object to an array.
     *
     * @return array
     */
    public function to_array(): array
    {
        // If this exception is container, then it returns data of container.
        if($this->is_container())
        {
            $previous       = $this->get_previous();

            if($previous instanceof BaseExceptionI)
            {
                $res        = $previous->to_array();
            }
            else
            {
                $res =
                [
                    'type'      => get_class($previous),
                    'source'    => $this->get_source_for($previous),
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

            $message    = isset($this->get_data()['message']) ? $this->get_data()['message'] : '';
        }
        else
        {
            $message    = $this->getMessage();
        }

        return
        [
            'type'      => get_class($this),
            'source'    => $this->get_source(),
            'message'   => $message,
            'template'  => $this->template(),
            'code'      => $this->getCode(),
            'data'      => $this->get_data()
        ];
    }

    /**
     * Returns information about value type
     *
     * @param   mixed           $value      Value
     *
     * @return  string|array
     */
    protected function type_info(mixed $value): string|array
    {
        return $this->get_value_type($value);
    }

    /**
     * The method returns true if debug mode is on.
     *
     * @return      boolean
     */
    protected function is_debug(): bool
    {
        if(is_bool($this->is_debug))
        {
            return $this->is_debug;
        }

        return $this->is_debug = Registry::is_debug(get_class($this));
    }

    /**
     * The method saved debug data if debug mode is active.
     *
     * @param       array       $data           debug data
     * @return      BaseExceptionI
     */
    protected function set_debug_data(array $data)
    {
        if(!$this->is_debug())
        {
            return $this;
        }

        $this->debug_data   = $data;

        return $this;
    }
}