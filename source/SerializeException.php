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
     * @param       string|array        $reason         Reason of error
     * @param       object              $object         The object which must have been serialized
     * @param       string              $type           Type of serialize
     * @param       object              $src_object     The object where started the process
     */
    public function __construct($reason, $object = null, $type = 'phpserialize', $src_object = null)
    {
        if(!is_string($reason))
        {
            parent::__construct($reason);
            return;
        }

        parent::__construct
        ([
            'message'       => 'Serialize Failed',
            'reason'        => $reason,
            'type'          => $type,
            'object'        => $this->get_value_type($object),
            'src_object'    => $this->get_value_type($src_object)
        ]);
    }
}