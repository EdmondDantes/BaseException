<?php declare(strict_types=1);

namespace IfCastle\Exceptions;

/**
 * Main interface for BaseExceptionI
 */
interface BaseExceptionInterface
{
    /**
     * The System is unusable
     */
    final public const EMERGENCY    = 1;

    /**
     * Immediate action required
     */
    final public const ALERT         = 2;

    /**
     * Critical conditions
     */
    final public const CRITICAL      = 3;

    /**
     * Error conditions
     */
    final public const ERROR         = 4;

    /**
     * Warning conditions
     */
    final public const WARNING       = 5;

    /**
     * Normal but significant
     */
    final public const NOTICE        = 6;

    /**
     * Informational
     */
    final public const INFO          = 7;

    /**
     * 	Debug-level messages
     */
    final public const DEBUG         = 8;

    /**
     * Mode for raise of exception
     */
    final public const RISE          = false;
    /**
     * Mode for mute of exception
     */
    final public const MUTE          = true;
    /**
     * Mode, then function returns exception.
     */
    final public const RESULT        = 1;

    public function getMessage();
    public function getPrevious();
    public function getCode();
    public function getFile();
    public function getLine();
    public function getTrace();
    public function getTraceAsString();

    /**
     * Template message
     */
    public function template(): string;
    
    /**
     * @return string[]
     */
    public function getTags(): array;
    
    /**
     * The method sets a logging flag.
     *
     * If set flag from TRUE to FALSE,
     * then the exception will not be saved to log (maybe).
     *
     * @param   boolean         $flag logging flag
     *
     * @return  $this
     */
    public function setLoggable(bool $flag): static;

    /**
     * The method returns a logging flag.
     *
     * TRUE - indicates that an exception is going to be written to the log.
     */
    public function isLoggable(): bool;

    /**
     * The method returns TRUE - if an exception is fatal.
     */
    public function isFatal(): bool;

    /**
     * Method marks the exception as fatal.
     *
     * Calling this method may lead to a call handler fatal errors.
     *
     * @return  $this
     */
    public function markAsFatal(): static;

    /**
     * The method will return true, if an exception is the container.
     */
    public function isContainer(): bool;

    /**
     * The method returns an error level
     */
    public function getLevel(): int;

    /**
     * The method returns the source of error.
     *
     * The method returns an array of values:
     * [
     *      'source'    => class name or file name, where the exception occurred
     *      'type'      => type of the call
     *      'function'  => function or method or closure
     * ]
     *
     * Attention the order of elements in the array is important!
     */
    public function getSource(): ?array;

    /**
     * The method returns previous exception.
     *
     * It extends the method Exception::getPrevious,
     * and it allows to work with objects which not inherited from Exception class,
     * but they are instances of BaseExceptionI.
     *
     * Also if this exception is container, when that method may be used
     * for getting contained object of BaseExceptionI.
     */
    public function getPreviousException(): \Throwable|BaseExceptionInterface|null;

    /**
     * The method returns extra data for exception
     */
    public function getExceptionData(): array;

    /**
     * @param       array       $data   The additional data
     */
    public function appendData(array $data): static;

    /**
     * The method returns debug data for exception
     */
    public function getDebugData(): array;

    /**
     * The method serialized object to an array.
     */
    public function toArray(): array;
}