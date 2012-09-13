<?PHP
namespace Exceptions;

/**
 * Исключение бросается, если класс не реализовывает
 * нужный интерфейс.
 */
class InterfaceNotImplemented  extends LoggableException
{
    /**
     * Конструктор исключения
     *
     * @param       string|array|object     $class         Имя класса
     * @param       string                  $interface     Интерфейс
     */
    public function __construct($class, $interface)
    {
        if(is_object($class))
        {
            $class = get_class($class);
        }

        if(is_array($class))
        {
            parent::__construct($class);
        }
        else
        {
            parent::__construct
            (
                array
                (
                    'message'   => "Class '$class' does not implement interface $interface",
                    'class'     => $class,
                    'interface' => $interface
                )
            );
        }
    }
}
?>