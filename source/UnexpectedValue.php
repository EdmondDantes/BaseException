<?PHP
namespace Exceptions;

/**
 * The variable has unexpected value!
 */
class UnexpectedValue   extends LoggableException
{
    protected $template         = 'Unexpected value {name} occurred with type {type}';

    /**
     * The variable has unexpected value!
     *
     * @param       string|array        $name           Variable name
     *                                                  or list of parameters for exception
     * @param       mixed               $value          Value
     * @param       string              $rules          Rules description
     *
     */
    public function __construct($name, $value = null, $rules = null)
    {
        if(!is_scalar($name))
        {
            parent::__construct($name);
            return;
        }

        parent::__construct
        ([
            'name'        => $name,
            'value'       => $this->to_string($value),
            'message'     => $rules,
            'type'        => $this->type_info($value)
        ]);
    }
}