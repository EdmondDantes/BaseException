<?PHP
namespace Exceptions;

/**
 * Contract is not correctly
 * (used for trait when the object is abusing the Trait)
 */
class ContractNotCorrectly   extends LoggableException
{
    const PROP      = 'property';
    const INT       = 'interface';
    const METHOD    = 'method';

    /**
     * Contract is not correctly
     *
     * @param       object      $object     object used trait
     * @param       string      $type       type of contract
     * @param       string      $value      incorrect value
     * @param       string      $trait      trait name
     * @param       string      $notice     extended message
     */
    public function __construct($object, $type = self::PROP, $value = null, $trait = null, $notice = '')
    {
        if(!is_string($value))
        {
            $value          = self::truncate($value);
        }

        parent::__construct
        ([
            'message'       => 'Contract is not correctly',
            'object'        => $this->get_value_type($object),
            'type'          => $type,
            'value'         => $value,
            'trait'         => $trait,
            'notice'        => $notice
        ]);
    }
}
?>