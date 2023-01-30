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

    static public function createFromLastError(array $error = null): ?static
    {
        if($error === null) {
            return null;
        }
        
        return static::createError(
            $error['type'] ?? 0, $error['message'] ?? '', $error['file'] ?? '', $error['line'] ?? 0
        );
    }
    
    /**
     * Errors factory
     *
     * @param        int    $code    Class of error
     * @param        string $message Message
     * @param        string $file    File
     * @param        int    $line    Line
     *
     * @return       Error
    */
    static public function createError(int $code, string $message, string $file, int $line): static
    {
        if(!array_key_exists($code, self::$ERRORS))
        {
            $code                   = self::ERROR;
        }

        if(in_array($code, array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE)))
        {
            return new UserError($code, $message, $file, $line);
        }

        switch(self::$ERRORS[$code])
        {
            case self::EMERGENCY    :
            {
                //
                // EMERGENCY created as fatal error
                //
                $err = new Error($code, $message, $file, (int)$line);
                $err->markAsFatal();

                return $err;
            }
            case self::WARNING  :
            {
                return new Warning($code, $message, $file, (int)$line);
            }
            case self::NOTICE   :
            case self::INFO     :
            case self::DEBUG    :
            {
                return new Notice($code, $message, $file, (int)$line);
            }
            case self::ALERT    :
            case self::CRITICAL :
            case self::ERROR    :
            default:
            {
                return new Error($code, $message, $file, (int)$line);
            }
        }
    }

    /**
     * Errors constructor
    */
    public function __construct(protected int $code, protected string $message, protected string $file, protected int $line)
    {
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