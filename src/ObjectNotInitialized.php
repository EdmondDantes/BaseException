<?php declare(strict_types=1);
namespace Exceptions;

/**
 * If object not initialized
 * but using!
 */
class ObjectNotInitialized          extends LoggableException
{
    protected string $template      = 'Object {object} is not initialized';

    /**
     * If object not initialized
     *
     * @param   object      $object     Object
     * @param   string      $message    Addition message
     */
    public function __construct($object = null, $message = '')
    {
        parent::__construct(['object' => $this->typeInfo($object), 'message' => $message]);
    }
}