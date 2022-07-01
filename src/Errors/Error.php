<?php

declare(strict_types=1);

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
	protected static array $ERRORS =
    [
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
	];

    protected string $message;
    protected int $code;
    protected string $file;
    protected int $line;
    protected ?array $trace;

    /**
     * Loggable flag
     *
     * @var         boolean
     */
    protected bool $isLoggable      = true;

    /**
     * Fatal error flag
     * @var         boolean
     */
    protected bool $isFatal         = false;

    /**
     * Errors factory
     *
     * @param        int            $errno      Class of error
     * @param        string         $errstr     Message
     * @param        string         $errfile    File
     * @param        string         $errline    Line
     *
     * @return       Error
    */
    static public function createError(int $errno, string $errstr, string $errfile, string $errline): static
    {
        if(!array_key_exists($errno, self::$ERRORS))
        {
            $errno = self::ERROR;
        }

        if(in_array($errno, array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE)))
        {
            return new UserError($errno, $errstr, $errfile, $errline);
        }

        switch(self::$ERRORS[$errno])
        {
            case self::EMERGENCY    :
            {
                //
                // EMERGENCY created as fatal error
                //
                $err = new Error($errno, $errstr, $errfile, (int)$errline);
                $err->markAsFatal();

                return $err;
            }
            case self::ALERT    :
            case self::CRITICAL     :
            case self::ERROR      :
            {
                return new Error($errno, $errstr, $errfile, (int)$errline);
            }
            case self::WARNING  :
            {
                return new Warning($errno, $errstr, $errfile, (int)$errline);
            }
            case self::NOTICE   :
            case self::INFO     :
            case self::DEBUG    :
            {
                return new Notice($errno, $errstr, $errfile, (int)$errline);
            }
            default:
            {
                return new Error($errno, $errstr, $errfile, (int)$errline);
            }
        }
    }

    /**
     * Errors constructor
     *
     * @param        int            $errno      Class of error
     * @param        string         $errstr     Message
     * @param        string         $errfile    File
     * @param        int            $errline    Line
     *
    */
    public function __construct(int $errno, string $errstr, string $errfile, int $errline)
    {
        $this->code    = $errno;
        $this->message = $errstr;
        $this->file    = $errfile;
        $this->line    = $errline;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getPrevious()
    {
        return null;
    }
    
    public function getTags(): array
    {
        return [];
    }
    
    public function getCode(): int
    {
        return $this->code;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getTrace(): ?array
    {
        return $this->trace;
    }

    public function getTraceAsString(): string
    {
        if(empty($this->trace))
        {
            return '';
        }

        return print_r($this->trace, true);
    }

    public function isLoggable(): bool
    {
        return $this->isLoggable;
    }

    public function setLoggable(bool $flag): static
    {
        $this->isLoggable = $flag;

        return $this;
    }

    public function isFatal(): bool
    {
        return $this->isFatal;
    }

    public function markAsFatal(): static
    {
        $this->isFatal = true;

        return $this;
    }

    public function isContainer(): bool
    {
        return false;
    }

    /**
     * Returns level of error
     * @return      int
     */
    public function getLevel(): int
    {
        if(!array_key_exists($this->code, self::$ERRORS))
        {
            return self::ERROR;
        }

        return self::$ERRORS[$this->code];
    }
    
    public function getSource(): array
    {
        return ['source' => $this->getFile(), 'type' => '', 'function' => ''];
    }

    public function getPreviousException(): \Throwable|BaseExceptionI|null
    {
        return null;
    }

    public function getExceptionData(): array
    {
        return [];
    }

    public function getDebugData(): array
    {
        return [];
    }

    public function toArray(): array
    {
        return
        [
            'type'      => get_class($this),
            'source'    => $this->getSource(),
            'message'   => $this->getMessage(),
            'code'      => $this->getCode(),
            'data'      => []
        ];
    }

    public function appendData(array $data): static
    {
        /** nothing to do */
        return $this;
    }

    public function template(): string
    {
        return '';
    }
}