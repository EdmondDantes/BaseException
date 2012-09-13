<?PHP
namespace Exceptions;

/**
 * Исключение срабатывает когда класс отсутствует
 * в проекте.
 */
class ClassNotExist  extends LoggableException
{
    /**
     * Конструктор исключения
     *
     * @param       string|array    $class         Имя класса
     */
    public function __construct($class)
    {
        if(!is_scalar($class))
        {
            parent::__construct($class);
        }
        else
        {
            parent::__construct
            (
                array
                (
                    'message' => "Сlass '$class' does not exist",
                    'class'   => $class
                )
            );
        }
    }
}
?>