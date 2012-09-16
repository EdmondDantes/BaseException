<?PHP
namespace Exceptions;

/**
 * Object can't be unserialized!
 */
class UnSerializeException  extends LoggableException
{
    /**
     * Object can't be unserialized!
     *
     * @param       object      $object         The object which must have been serialized
     * @param       string      $type           Type of serialize
     * @param       object      $src_object     The object where started the process
     */
    public function __construct($object = null, $type = 'phpserialize', $src_object = null)
    {
        parent::__construct
        ([
            'message'       => 'UnSerialize Failed',
            'type'          => $type,
            'object'        => $this->get_value_type($object),
            'src_object'    => $this->get_value_type($src_object)
        ]);
    }
}
?>