<?PHP
namespace Exceptions\Errors;

use \Exceptions\BaseExceptionI;

/**
 * Класс для инкапсулирования ошибок PHP,
 * как BaseExceptionI объект.
 *
 */
class Error implements BaseExceptionI
{
	/**
	 * Определения типов ошибок PHP и типов ошибок BaseExceptionI
	 * @var array
	 */
	protected static $ERRORS = array
    (
        E_ERROR              => self::ERR,
        E_WARNING            => self::WARNING,
        E_PARSE              => self::CRIT,
        E_NOTICE             => self::NOTICE,
        E_CORE_ERROR         => self::EMERG,
        E_CORE_WARNING       => self::WARNING,
        E_COMPILE_ERROR      => self::EMERG,
        E_COMPILE_WARNING    => self::WARNING,
        E_USER_ERROR         => self::ERR,
        E_USER_WARNING       => self::INFO,
        E_USER_NOTICE        => self::DEBUG,
        E_STRICT             => self::ERR,
        E_RECOVERABLE_ERROR  => self::ERR,
        E_DEPRECATED         => self::INFO,
        E_USER_DEPRECATED    => self::INFO
	);

    protected $message;
    protected $code;
    protected $file;
    protected $line;
    protected $trace;

    /**
     * Флаг логирования.
     * Если флаг равен true - то исключение
     * собирается быть записанным в журнал.
     *
     * @var         boolean
     */
    protected $is_loggable  = true;

    /**
     * Флаг фатальной ошибки
     * @var         boolean
     */
    protected $is_fatal     = false;

    /**
     * Конструктор ошибки
     *
     * @param        int            $errno      Класс ошибки
     * @param        string         $errstr     Описание
     * @param        string         $errfile    Файл
     * @param        string         $errline    Номер строки в файле
     * @param        array          $errcontext Контекст ошибки
     *
     * @return       Error
    */
    static public function create_error($errno, $errstr, $errfile, $errline, $errcontext = null)
    {
        if(!array_key_exists($errno, self::$ERRORS))
        {
            $errno = self::ERR;
        }

        if(in_array($errno, array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE)))
        {
            return new UserError($errno, $errstr, $errfile, $errline, $errcontext);
        }

        switch(self::$ERRORS[$errno])
        {
            case self::EMERG    :
            {
                //
                // EMERG преобразуются в фатальные ошибки
                //
                $err = new Error($errno, $errstr, $errfile, $errline, $errcontext);
                $err->set_fatal();

                return $err;
            }
            case self::ALERT    :
            case self::CRIT     :
            case self::ERR      :
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
        }

    }

    /**
     * Конструктор ошибки
     *
     * @param        int            $errno      Класс ошибки
     * @param        string         $errstr     Описание
     * @param        string         $errfile    Файл
     * @param        string         $errline    Номер строки в файле
     * @param        array          $errcontext Контекст ошибки
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

    /**
     * Метод возвращает флаг журнализирования
     * для ошибки.
     *
     * Если метод вернёт true - это значит,
     * что исключение собирается быть поданым в журнал.
     *
     * @return boolean
     */
    public function is_loggable()
    {
        return $this->is_loggable;
    }

    /**
     * Метод устанавливает флаг журналирования исключения.
     * Если $flag равно true, тогда исключение попадает в журнал.
     * Иначе - исключение игнорируется.
     *
     * @param   boolean     $flag   Состояние флага журналирования
     *
     * @return  Error               Метод возвращает указатель на себя
     */
    public function set_loggable($flag)
    {
        $this->is_loggable = (boolean) $flag;

        return $this;
    }

    /**
     * Метод возвращает true - если ошибка является фатальной.
     *
     * @return boolean
     */
    public function is_fatal()
    {
        return $this->is_fatal;
    }

    /**
     * Метод отмечает ошибку фатальной.
     *
     * @return  Error       Метод возвращает указатель на себя
     */
    public function set_fatal()
    {
        $this->is_fatal = true;

        return $this;
    }

    /**
     * Метод вернёт true, если ошибка - это контейнер
     * @return boolean
     */
    public function is_container()
    {
        return false;
    }

    /**
     * Метод вернёт уровень ошибки.
     * @return      int
     */
    public function get_level()
    {
        if(!array_key_exists($this->code, self::$ERRORS))
        {
            return self::ERR;
        }

        return self::$ERRORS[$this->code];
    }

    /**
     * Метод определяет источник ошибки
     * @return array
     */
    public function get_source()
    {
        return ['source' => $this->getFile(), 'type' => '', 'function' => ''];
    }

    public function get_previous()
    {
        return null;
    }

    /**
     * Метод возвращает дополнительные данные исключения
     * @return array
     */
    public function get_data()
    {
        return array();
    }

    /**
     * Метод вернёт отладочные данные
     * @return string
     */
    public function get_debug_data()
    {
        return '';
    }

    /**
     * Метод возвращает ошибку в виде массива.
     *
     * @return array
     */
    public function to_array()
    {
        return array
        (
            'message'   => $this->getMessage(),
            'code'      => $this->getCode(),
            'data'      => array()
        );
    }
}

?>