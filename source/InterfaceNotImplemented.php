<?PHP
namespace Exceptions;

/**
 * If class not implimented required interface
 */
class InterfaceNotImplemented  extends LoggableException
{
    /**
     * Конструктор исключения
     *
     * @param       string|array|object     $class         Class name
     * @param       string                  $interface     Required interface
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