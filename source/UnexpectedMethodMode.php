<?PHP
namespace Exceptions;

/**
 * The method does not support this mode of work.
 */
class UnexpectedMethodMode  extends LoggableException
{
    protected $template         = 'Unexpected method mode occurred (method: {method}, mode: {mode} = {value})';

    /**
     * The method does not support this mode of work.
     *
     * @param       string|array    $method         The method name
     *                                              or list of parameters for exception
     *                                              or another exception for container
     * @param       string          $mode           Name of mode
     * @param       string|int      $value          Mode value
     */
    public function __construct($method,
                                $mode   = null,
                                $value  = null)
    {
        if(!is_scalar($method))
        {
            parent::__construct($method);
        }
        else
        {
            parent::__construct
            ([
                'method'  => $method,
                'mode'    => $mode,
                'value'   => $value
            ]);
        }
    }
}