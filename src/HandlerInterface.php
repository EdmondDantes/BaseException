<?php declare(strict_types=1);

namespace IfCastle\Exceptions;

interface HandlerInterface
{
    /**
     * Exception handler
     *
     *
     */
    public function exceptionHandler(\Throwable|BaseExceptionInterface $exception): void;
}