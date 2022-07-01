<?php declare(strict_types=1);
namespace Exceptions;

/**
 * Fatal exception with aspect: "Runtime"
 */
class FatalRuntimeException extends FatalException implements RuntimeExceptionI
{
}