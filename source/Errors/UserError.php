<?PHP
namespace Exceptions\Errors;

/**
 * Class for USER_ error:
 *
 * -  E_USER_ERROR is logged,
 * -  WARNING ни NOTICE - not is logged.
 *
 */
class UserError extends Error implements \Exceptions\RuntimeExceptionI
{
    public function __construct($errno, $errstr, $errfile, $errline, $errcontext = null)
    {
        parent::__construct($errno, $errstr, $errfile, $errline, $errcontext);

        if($this->code !== E_USER_ERROR)
        {
            $this->is_loggable = false;
        }
    }
}

?>