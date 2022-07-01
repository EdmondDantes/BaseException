<?php declare(strict_types=1);
namespace Exceptions;

/**
 * Special exception, which is used to mark an unhandled exception.
 * Is used in the `Registry`.
 */
class UnhandledException            extends LoggableException
{
    protected string $template      = 'Unhandled Exception {type} occurred in the {source}';

    /**
     * @param \Throwable|BaseExceptionI $exception
     */
    public function __construct(\Throwable $exception)
    {
        parent::__construct
        ([
            'type'      => $this->typeInfo($exception),
            'source'    => $this->getSourceFor($exception),
            'previous'  => $exception
        ]);
    }
}