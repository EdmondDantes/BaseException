<?PHP
namespace Exceptions;

/**
 * Rais if the method can not be called
 */
class MethodNotCallable extends LogicalException
{
    /**
     * MethodNotCallable
     *
     * @param       string|array        $method         Method
     * @param       string              $message        Message
     */
    public function __construct($method, $message = null)
    {
        if(!is_scalar($method))
        {
            parent::__construct($method);
        }
        else
        {
            parent::__construct(array('method'  => $method,
                                      'message' => $message));
        }
    }
}
?>
