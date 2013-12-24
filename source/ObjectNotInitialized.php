<?PHP
namespace Exceptions;

/**
 * If object not initialized
 * but using!
 */
class ObjectNotInitialized  extends LoggableException
{
    protected $template     = 'Object {object} is not initialized';

    /**
     * If object not initialized
     *
     * @param   object      $object     Object
     * @param   string      $message    Addition message
     */
    public function __construct($object = null, $message = '')
    {
        parent::__construct(['object' => $this->type_info($object), 'message' => $message]);
    }
}