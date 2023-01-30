<?php declare(strict_types=1);
namespace Exceptions;

interface HandlerI
{
    /**
     * Exception handler
     *
     * @param       \Throwable|BaseExceptionI   $exception
     *
     * @return      void
     */
    public function exceptionHandler(\Throwable|BaseExceptionI $exception): void;
}