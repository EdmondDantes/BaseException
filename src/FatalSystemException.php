<?php declare(strict_types=1);
namespace Exceptions;

/**
 * Fatal exception with aspect: "System"
 */
class FatalSystemException extends FatalException implements SystemExceptionI
{
}