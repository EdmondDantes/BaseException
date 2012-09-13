<?PHP
namespace Exceptions\Errors;

/**
 * Класс для преобразования ошибок,
 * которые являются нотисами.
 * 
 * Ошибки с кодом E_USER_ERROR - попадают в журнал.
 * Однако ни WARNING ни NOTICE - это не касается.
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