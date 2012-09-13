<?PHP
namespace Exceptions;

/**
 * Базовый класс для логируемых исключений
 */
class LoggableException extends BaseException
{
    /**
     * Флаг логирования.
     * Если флаг равен true - то исключение
     * собирается быть записанным в журнал.
     *
     * @var         boolean
     */
    protected $is_loggable  = true;
}
?>