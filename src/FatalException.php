<?php declare(strict_types=1);
namespace IfCastle\Exceptions;

/**
 * Fatal exception - container.
 *
 * The class used as container for another exceptions.
 *
 * It is marked $exception as "fatal" and logged its
 *
 */
class FatalException                extends LoggableException
{
    /**
     * FatalException
     *
     * @param       \Throwable|mixed    $exception
     * @param       ?\Throwable         $previous
     */
    public function __construct(mixed $exception, int $code = 0, \Throwable $previous = null)
    {
        if($exception instanceof BaseExceptionInterface)
        {
            parent::__construct($exception->markAsFatal());
        }
        else
        {
            $this->isFatal = true;
            parent::__construct($exception, $code, $previous);
        }
    }
}