<?php declare(strict_types=1);
namespace IfCastle\Exceptions;

/**
 * Base class for loggable exception.
 */
class LoggableException extends BaseException
{
    /**
     * Loggable flag is true.
     */
    protected bool $isLoggable      = true;
}