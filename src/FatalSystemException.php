<?php declare(strict_types=1);
namespace IfCastle\Exceptions;

/**
 * Fatal exception with aspect: "System"
 */
class FatalSystemException extends FatalException implements SystemExceptionInterface
{
}