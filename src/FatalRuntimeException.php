<?php declare(strict_types=1);
namespace IfCastle\Exceptions;

/**
 * Fatal exception with aspect: "Runtime"
 */
class FatalRuntimeException extends FatalException implements RuntimeExceptionInterface
{
}