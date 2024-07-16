<?php declare(strict_types=1);
namespace IfCastle\Exceptions;

interface SaveHandlerInterface
{
    /**
     * Save handler method
     *
     *
     */
    public function saveExceptions(array $exceptions , callable $resetLog, array $loggerOptions = [], array $debugOptions = []): void;
}