<?php declare(strict_types=1);
namespace Exceptions;

/**
 * Base class for loggable exception.
 */
class LoggableException extends BaseException
{
    /**
     * Loggable flag is true.
     *
     * @var         boolean
     */
    protected bool $isLoggable      = true;
}