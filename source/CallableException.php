<?PHP
namespace Exceptions;

/**
 * Raise if the expression is not callable.
 * (Usually when using function is_callable)
 */
class CallableException extends LoggableException
{
    protected string $template     = 'Expression {expression} is not callable';
    
    /**
     * Expression is not callable!
     *
     * @param mixed                 $expression Expression
     */
    public function __construct(mixed $expression)
    {
        parent::__construct
        ([
            'expression'    => $this->to_string($expression)
        ]);
    }
}