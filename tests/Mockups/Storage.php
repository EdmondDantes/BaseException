<?php

declare(strict_types=1);

namespace IfCastle\Exceptions\Mockups;

use IfCastle\Exceptions\BaseExceptionInterface;
use IfCastle\Exceptions\StorageInterface;

class Storage implements StorageInterface
{
    public array $Exceptions = [];

    /**
     *
     *
     * @param       BaseExceptionInterface|\Throwable $exception
     *
     * @return      StorageInterface
     */
    public function addException(BaseExceptionInterface|\Throwable $exception): static
    {
        $this->Exceptions[] = $exception;

        return $this;
    }

    public function getStorageExceptions(): array
    {
        return $this->Exceptions;
    }

    public function resetStorage(): static
    {
        $this->Exceptions = array();

        return $this;
    }
}