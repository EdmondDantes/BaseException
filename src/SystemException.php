<?php declare(strict_types=1);
namespace Exceptions;

/**
 * Base class for exception with aspect: System
 */
class SystemException extends LoggableException implements SystemExceptionI
{
}