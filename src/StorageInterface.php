<?php declare(strict_types=1);
namespace IfCastle\Exceptions;

/**
 * StorageI - Interface for exception storage.
 *
 */
interface StorageInterface
{
    /**
     * Add exception into storage
     *
     * @param       BaseExceptionInterface|\Exception $exception Exception
     */
    public function addException(BaseExceptionInterface|\Throwable $exception): static;

    /**
     * Returns list of exceptions
     */
    public function getStorageExceptions(): array;

    /**
     * Reset storage
     */
    public function resetStorage(): static;
}