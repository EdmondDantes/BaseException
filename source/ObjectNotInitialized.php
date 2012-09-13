<?PHP
namespace Exceptions;

/**
 * Исключение бросается, если при выполнении
 * метода объект находится в неинициализированном состоянии
 */
class ObjectNotInitialized  extends LoggableException
{
    public function __construct($object = null)
    {
        if (is_object($object))
        {
            $object = get_class($object);
        }
        else
        {
            $object = gettype($object);
        }

        parent::__construct("Object not initialized (object = '$object')");
    }
}
?>