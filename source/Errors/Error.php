<?PHP
namespace Exceptions\Errors;

use Exceptions\BaseExceptionI;

/**
 * The class for encapsulate of PHP Errors
 * as object BaseExceptionI
 */
class Error implements BaseExceptionI
{
	/**
	 * Conformity between PHP-errors and BaseExceptionI
	 * @var array
	 */
	protected static $ERRORS = array
    (
        E_ERROR              => self::ERROR,
        E_WARNING            => self::WARNING,
        E_PARSE              => self::CRITICAL,
        E_NOTICE             => self::NOTICE,
        E_CORE_ERROR         => self::EMERGENCY,
        E_CORE_WARNING       => self::WARNING,
        E_COMPILE_ERROR      => self::EMERGENCY,
        E_COMPILE_WARNING    => self::WARNING,
        E_USER_ERROR         => self::ERROR,
        E_USER_WARNING       => self::INFO,
        E_USER_NOTICE        => self::DEBUG,
        E_STRICT             => self::ERROR,
        E_RECOVERABLE_ERROR  => self::ERROR,
        E_DEPRECATED         => self::INFO,
        E_USER_DEPRECATED    => self::INFO
	);

    protected $message;
    protected $code;
    protected $file;
    protected $line;
    protected $trace;

    /**
     * Loggable flag
     *
     * @var         boolean
     */
    protected $is_loggable  = true;

    /**
     * Fatal error flag
     * @var         boolean
     */
    protected $is_fatal     = false;

    /**
     * Errors factory
     *
     * @param        int            $errno      Class of error
     * @param        string         $errstr     Message
     * @param        string         $errfile    File
     * @param        string         $errline    Line
     * @param        array          $errcontext Context
     *
     * @return       Error
    */
    static public function create_error($errno, $errstr, $errfile, $errline, $errcontext = null)
    {
        if(!array_key_exists($errno, self::$ERRORS))
        {
            $errno = self::ERROR;
        }

        if(in_array($errno, array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE)))
        {
            return new UserError($errno, $errstr, $errfile, $errline, $errcontext);
        }

        switch(self::$ERRORS[$errno])
        {
            case self::EMERGENCY    :
            {
                //
                // EMERGENCY created as fatal error
                //
                $err = new Error($errno, $errstr, $errfile, $errline, $errcontext);
                $err->set_fatal();

                return $err;
            }
            case self::ALERT    :
            case self::CRITICAL     :
            case self::ERROR      :
            {
                return new Error($errno, $errstr, $errfile, $errline, $errcontext);
            }
            case self::WARNING  :
            {
                return new Warning($errno, $errstr, $errfile, $errline, $errcontext);
            }
            case self::NOTICE   :
            case self::INFO     :
            case self::DEBUG    :
            {
                return new Notice($errno, $errstr, $errfile, $errline, $errcontext);
            }
            default:
            {
                return new Error($errno, $errstr, $errfile, $errline, $errcontext);
            }
        }
    }

    /**
     * Errors constructor
     *
     * @param        int            $errno      Class of error
     * @param        string         $errstr     Message
     * @param        string         $errfile    File
     * @param        string         $errline    Line
     * @param        array          $errcontext Context
     *
    */
    public function __construct($errno, $errstr, $errfile, $errline, $errcontext = null)
    {
        $this->code    = $errno;
        $this->message = $errstr;
        $this->file    = $errfile;
        $this->line    = $errline;
        $this->trace   = $errcontext;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getPrevious()
    {
        return null;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getLine()
    {
        return $this->line;
    }

    public function getTrace()
    {
        return $this->trace;
    }

    public function getTraceAsString()
    {
        if(empty($this->trace))
        {
            return '';
        }

        return print_r($this->trace, true);
    }

    public function is_loggable()
    {
        return $this->is_loggable;
    }

    public function set_loggable($flag)
    {
        $this->is_loggable = (boolean) $flag;

        return $this;
    }

    public function is_fatal()
    {
        return $this->is_fatal;
    }

    public function set_fatal()
    {
        $this->is_fatal = true;

        return $this;
    }

    public function is_container()
    {
        return false;
    }

    /**
     * Returns level of error
     * @return      int
     */
    public function get_level()
    {
        if(!array_key_exists($this->code, self::$ERRORS))
        {
            return self::ERROR;
        }

        return self::$ERRORS[$this->code];
    }

    public function get_source()
    {
        return ['source' => $this->getFile(), 'type' => '', 'function' => ''];
    }

    public function get_previous()
    {
        return null;
    }

    public function get_data()
    {
        return array();
    }

    public function get_debug_data()
    {
        return '';
    }

    public function to_array()
    {
        return array
        (
            'type'      => get_class($this),
            'source'    => $this->get_source(),
            'message'   => $this->getMessage(),
            'code'      => $this->getCode(),
            'data'      => array()
        );
    }

    public function append_data(array $data)
    {
        /** nothing to do */
        return $this;
    }

    public function template()
    {
        return '';
    }
}