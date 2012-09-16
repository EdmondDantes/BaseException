<?PHP
namespace Exceptions;

/**
 * Object can't be serialized!
 */
class SerializeException  extends LoggableException
{
    /**
     * Object can't be serialized!
     *
     * @param       object|array        $object         The object which must have been serialized
     * @param       string              $type           Type of serialize
     * @param       object              $src_object     The object where started the process
     */
    public function __construct($object = null, $type = 'phpserialize', $src_object = null)
    {
        if(is_array($object))
        {
            parent::__construct($object);
            return;
        }

        parent::__construct
        ([
            'message'       => 'Serialize Failed',
            'type'          => $type,
            'object'        => $this->get_value_type($object),
            'src_object'    => $this->get_value_type($src_object)
        ]);
    }
}