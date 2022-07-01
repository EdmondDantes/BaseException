<?php declare(strict_types=1);
namespace Exceptions;

/**
 * StorageI - Interface for exception storage.
 *
 */
interface StorageI
{
    /**
     * Add exception into storage
     *
     * @param       BaseExceptionI|\Exception      $exception      Exception
     *
     * @return      StorageI
     */
    public function addException(BaseExceptionI|\Throwable $exception): static;

    /**
     * Returns list of exceptions
     *
     * @return      array
     */
    public function getStorageExceptions(): array;

    /**
     * Reset storage
     *
     * @return      StorageI
     */
    public function resetStorage(): static;
}