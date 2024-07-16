<?php declare(strict_types=1);
namespace IfCastle\Exceptions;

/**
 * Raise if the method can not be called
 */
class MethodNotCallable     extends LogicalException
{
    protected string $template      = 'The method {method} is not callable';
    
    /**
     * MethodNotCallable
     *
     * @param   string|array        $method  Method
     * @param   string              $message Extended Message
     */
    public function __construct(array|string $method, $message = '')
    {
        if(!is_scalar($method))
        {
            parent::__construct($method);
        }
        else
        {
            parent::__construct(['method'  => $this->toString($method), 'message' => $message]);
        }
    }
}