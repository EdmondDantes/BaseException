<?PHP
namespace Exceptions;

/**
 * Rais if the expression is not callable.
 * (Usually when using function is_callable)
 */
class CallableException extends LoggableException
{
    /**
     * Expression is not callable!
     *
     * @param       mixed        $expression     Expression
     */
    public function __construct($expression)
    {
        if(is_object($expression) || is_resource($expression))
        {
            $expression = self::to_string($expression);
        }

        parent::__construct
        ([
            'message'       => 'Expression is not callable',
            'expression'    => $expression
        ]);
    }
}