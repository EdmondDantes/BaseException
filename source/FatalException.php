<?PHP
namespace Exceptions;

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
     * @param       int                 $code
     * @param       \Throwable          $previous
     */
    public function __construct($exception, $code = 0, $previous = null)
    {
        if($exception instanceof BaseExceptionI)
        {
            parent::__construct($exception->set_fatal());
        }
        else
        {
            $this->is_fatal = true;
            parent::__construct($exception, $code = 0, $previous);
        }
    }
}