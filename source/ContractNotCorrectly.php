<?PHP
namespace Exceptions;

/**
 * Contract is not correctly
 * (When an object does not support the required conditions of Trait,
 * Traits
 * throws that exception)
 */
class ContractNotCorrectly      extends LoggableException
{
    const PROP                  = 'property';
    const INT                   = 'interface';
    const METHOD                = 'method';

    protected $template         = 'Contract is not correctly for {type} in the trait {trait} which used by {object}';

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
        parent::__construct
        ([
            'message'       => $notice,
            'object'        => $this->type_info($object),
            'type'          => $type,
            'value'         => $this->to_string($value),
            'trait'         => $trait
        ]);
    }
}