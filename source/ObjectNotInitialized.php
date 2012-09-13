<?PHP
namespace Exceptions;

/**
 * If object not initialized
 * but using!
 */
class ObjectNotInitialized  extends LoggableException
{
    /**
     * If object not initialized
     *
     * @param object $object
     */
    public function __construct($object = null)
    {
        parent::__construct("Object not initialized (object = '{$this->get_value_type($object)}')");
    }
}
?>