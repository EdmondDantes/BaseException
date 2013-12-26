<?PHP
namespace Exceptions;

/**
 * Raise if the method can not be called
 */
class MethodNotCallable     extends LogicalException
{
    protected $template     = 'The method {method} is not callable';

    /**
     * MethodNotCallable
     *
     * @param       string|array        $method         Method
     * @param       string              $message        Extended Message
     */
    public function __construct($method, $message = '')
    {
        if(!is_scalar($method))
        {
            parent::__construct($method);
        }
        else
        {
            parent::__construct(['method'  => $this->to_string($method), 'message' => $message]);
        }
    }
}