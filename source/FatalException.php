<?PHP
namespace Exceptions;

/**
 * Фатальное исключение - контейнер.
 *
 * Исключение используется как контейнер для других исключений.
 *
 * Если в конструктор передано исключение типа BaseExceptionI,
 * то FatalException не будет журнализирован.
 *
 * Иначе - исключение будет журнализировано,
 * и примет на себя все параметры от исходного.
 *
 */
class FatalException extends LoggableException
{
    /**
     * Конструктор фатального исключения.
     *
     * @param       \Exception|mixed    $exception
     * @param       int                 $code
     * @param       \Exception          $previous
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
?>