<?PHP
namespace Exceptions;

/**
 * Base class for loggable exception.
 */
class LoggableException extends BaseException
{
    /**
     * Loggable flag is true.
     *
     * @var         boolean
     */
    protected $is_loggable  = true;
}
?>