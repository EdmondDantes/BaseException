<?PHP
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
 */
class BaseException extends \Exception implements BaseExceptionI
{
    /**
     * Layout of the default properties
     * @var array
     */
    static protected $base_props = array('message' => '', 'code' => 0, 'previous' => null);

    /**
     * Extra data to exception
     * @var         array
     */
    protected $data         = [];

    /**
     * Source of error
     * @var         array
     */
    protected $source;

    /**
     * Debug data
     * @var         mixed
     */
    protected $debug_data;

    /**
     * Container flag
     * @var         boolean
     */
    protected $is_container = false;

    /**
     * Logged flag.
     * If it's equal true,
     * is going to be recorded in the journal.
     *
     * @var         boolean
     */
    protected $is_loggable  = false;

    /**
     * Fatal exception flag
     *
     * @var         boolean
     */
    protected $is_fatal     = false;

    /**
     * Debug mode flag
     * @var         boolean
     */
    protected $is_debug;

    /**
     * BaseException constructor.
     *
     * Use Cases:
     *
     * 1. $exception - string. In this case, the parameters involved: $code, $previous.
     *    and $exception is the $message.
     * 2. $exception - array.
     *    Key of array: message, code, previous
     *    ovverided the parameters $code and $previous,
     *    and other data is saved to the property `$data`.
     * 3. $exception - BaseExceptionI
     *    In this case, exception acts as container,
     *    but it does not inherit a data from the $exception.
     * 4. $exception - \Exception -
     *    In this case, exception acts as container,
     *    and inherits a data from the $exception
     *
     * @param 		mixed 				$exception      Exception
     * @param 		int 				$code           Code
     * @param 		\Exception 			$previous       Previous or aggregate exception
     */
    public function __construct($exception, $code = 0, $previous = null)
    {
        $message                = '';

        if($exception instanceof BaseExceptionI)
        {
            $this->is_container = true;

            // If aggregate $exception was't journaled,
            // and this is going to be to journal,
            // then an $exception should be registered.
            if(!$exception->is_loggable() && $this->is_loggable)
            {
                Registry::register_exception($exception);
            }

            $previous           = $exception;
        }
        elseif($exception instanceof \Exception)
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

        if( $previous instanceof BaseExceptionI
        && ($previous instanceof \Exception) === false)
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
     * The method returns a logging flag.
     *
     * TRUE - indicates that an exception is going to be written to the log.
     *
     * @return boolean
     */
    public function is_loggable()
    {
        return $this->is_loggable;
    }

    /**
     * The method sets a logging flag.
     *
     * If set flag from TRUE to FALSE,
     * then the exception will not be saved to log (may be).
     *
     * @param   boolean     $flag   logging flag
     *
     * @return  BaseException
     */
    public function set_loggable($flag)
    {
        $this->is_loggable = (boolean) $flag;

        return $this;
    }

    /**
     * The method returns TRUE - if an exception is fatal.
     *
     * @return boolean
     */
    public function is_fatal()
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
    public function is_container()
    {
        return $this->is_container;
    }

    /**
     * The method returns an error level
     * @return      int
     */
    public function get_level()
    {
        return empty($this->data['level']) ? self::ERR : $this->data['level'];
    }

    /**
     * The method returns the source of error.
     *
     * The method returns an array of values​​:
     * [
     *      'source'    => class name or file name, where the exception occurred
     *      'type'      => type of the call
     *      'function'  => function or method or closure
     * ]
     *
     * @return array
     */
    public function get_source()
    {
        if(is_array($this->source))
        {
            return $this->source;
        }

        return $this->source    = self::get_source_for($this);
    }

    /**
     * The method defines the source of the exception.
     *
     * @param       \Exception      $e
     * @param       boolean         $is_string
     *
     * @return      array|string
     */
    static public function get_source_for(\Exception $e, $is_string = false)
    {
        $res                    = $e->getTrace()[0];

        if($is_string)
        {
            return  isset($res['source'])     ? $res['class']     : $res['file'].':'.$res['line'].
                    isset($res['type'])       ? $res['type']      : '.'.
                    isset($res['function'])   ? $res['function']  : '{}';
        }

        return
        [
            'source'    => isset($res['class'])      ? $res['class']     : $res['file'].':'.$res['line'],
            'type'      => isset($res['type'])       ? $res['type']      : '.',
            'function'  => isset($res['function'])   ? $res['function']  : '{}',
        ];
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
     * @return      BaseExceptionI|\Exception|null
     */
    public function get_previous()
    {
        if(!$this->is_container())
        {
            return $this->getPrevious();
        }

        $res = $this->getPrevious();
        if($res instanceof \Exception)
        {
            return $res;
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
    public function get_data()
    {
        return $this->data;
    }

    /**
     * The method returns debug data for exception
     *
     * @return mixed
     */
    public function get_debug_data()
    {
        return $this->debug_data;
    }

    /**
     * The method serialized object to an array.
     *
     * @return array
     */
    public function to_array()
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
                    'source'    => self::get_source_for($previous),
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

        return
        [
            'type'      => get_class($this),
            'source'    => $this->get_source(),
            'message'   => $this->getMessage(),
            'code'      => $this->getCode(),
            'data'      => $this->get_data()
        ];
    }

    /**
     * The method serialized errors BaseExceptionI to an array
     *
     * @param 			array 			$errors		array of errors
     * @param 			boolean 		$debug		debug data flag
     */
    public static function errors_to_array($errors)
    {
        if($errors instanceof BaseExceptionI)
        {
            $errors = [$errors];
        }

        $res = [];
        foreach($errors as $error)
        {
            if($error instanceof BaseExceptionI)
            {
                /* @var BaseExceptionI $error */
                $res[] = $error->to_array();
            }
            elseif($error instanceof \Exception)
            {
                /* @var \Exception $error */
                $res[] =
                [
                    'type'     => get_class($error),
                    'source'   => self::get_source_for($error),
                    'message'  => $error->getMessage(),
                    'code'     => $error->getCode(),
                    'data'     => $error->getTrace()
                ];
            }
        }
        return $res;
    }

    /**
     * The method deserialized array of array to array of errors.
     *
     * @param 			array 						$array array of array
     *
     * @throws          \UnexpectedValueException
     */
    public static function array_to_errors($array)
    {
        if(!is_array($array))
        {
            throw new \UnexpectedValueException('$array must be array');
        }

        $res = array();
        foreach($array as $error)
        {
            if(!is_array($error))
            {
                throw new \UnexpectedValueException('$error must be array');
            }
            $res[] = new self($error);
        }

        return $res;
    }

    /**
     * The method returns a type of $value or class name.
     *
     * It must use in order to exclude objects from the exception.
     *
     * @param           mixed           $value      value
     *
     * @return          string
     */
    public static function get_value_type($value)
    {
        if(is_bool($value))
        {
            return 'BOOLEAN:'.($value ? 'TRUE' : 'FALSE');
        }
        elseif(is_object($value))
        {
            return get_class($value);
        }
        elseif(is_null($value))
        {
            return 'NULL';
        }
        elseif(is_string($value))
        {
            return 'STRING';
        }
        elseif(is_int($value))
        {
            return 'INTEGER';
        }
        elseif(is_float($value))
        {
            return 'DOUBLE';
        }
        elseif(is_array($value))
        {
            return 'ARRAY';
        }
        elseif(is_resource($value))
        {
            $type           = get_resource_type($value);
            $meta           = '';
            if($type === 'stream' && is_array($meta = stream_get_meta_data($value)))
            {
                // array keys normalize
                $meta       = array_merge
                (
                    ['stream_type' => '', 'wrapper_type' => '', 'mode' => '', 'uri' => ''],
                    $meta
                );
                $meta       = " ({$meta['stream_type']},{$meta['wrapper_type']},{$meta['mode']}) {$meta['uri']}";
            }

            return 'RESOURCE: '.$type.$meta;
        }
        else
        {
            return gettype($value);
        }
    }

    /**
     * The method returns true if debug mode is on.
     *
     * @return      boolean
     */
    protected function is_debug()
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
     * @param       mixed       $data           debug data
     * @return      BaseExceptionI
     */
    protected function set_debug_data(array $data)
    {
        if(!$this->is_debug())
        {
            return $this;
        }

        if(!is_string($data))
        {
            $data           = print_r($data, true);
        }

        $this->debug_data   = $data;

        return $this;
    }

    /**
     * The method truncate $value for exception journal.
     *
     * @param       mixed       $value
     *
     * @return      string
     */
    static public function truncate($value)
    {
        // Укорачивание данных
        if(is_string($value) && strlen($value) > 63)
        {
            $value          = substr($value, 0, 63).'…';
        }

        if(!is_scalar($value))
        {
            $value          = '!object '.self::get_value_type($value);
        }
        return $value;
    }
}
?>