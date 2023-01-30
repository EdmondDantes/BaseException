<?php declare(strict_types=1);
namespace Exceptions;

interface SaveHandlerI
{
    /**
     * Save handler method
     *
     * @param       array               $exceptions
     * @param       callable            $resetLog
     * @param       array               $loggerOptions
     * @param       array               $debugOptions
     *
     * @return      void
     */
    public function saveExceptions(array $exceptions , callable $resetLog, array $loggerOptions = [], array $debugOptions = []): void;
}