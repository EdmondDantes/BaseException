<?php declare(strict_types=1);

namespace IfCastle\Exceptions\Errors;

use IfCastle\Exceptions\RuntimeExceptionInterface;

/**
 * Class for USER_ error:
 *
 * -  E_USER_ERROR is logged,
 * -  WARNING or NOTICE - not is logged.
 *
 */
class UserError extends Error implements RuntimeExceptionInterface
{
    public function __construct(int $code, string $message, string $file, int $line)
    {
        parent::__construct($code, $message, $file, $line);

        if($this->code !== E_USER_ERROR)
        {
            $this->isLoggable = false;
        }
    }
}