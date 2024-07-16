<?php
declare(strict_types=1);

namespace IfCastle\Exceptions;

/**
 * ## ClientAvailableInterface
 *
 * Exception can be shown to the client
 */
interface ClientAvailableInterface
{
    public function getClientMessage(): string;
    
    /**
     * Serialize exception for client
     */
    public function clientSerialize(): array;
}