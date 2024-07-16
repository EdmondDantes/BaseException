<?php

declare(strict_types=1);

namespace Mockups;

use Exceptions\BaseExceptionInterface;
use Exceptions\StorageI;

class Storage implements \Exceptions\StorageI
{
    public array $Exceptions = [];

    /**
     *
     *
     * @param       BaseExceptionInterface|\Throwable $exception
     *
     * @return      StorageI
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