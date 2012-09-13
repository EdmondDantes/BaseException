<?PHP
namespace Exceptions;

/**
 * The variable has unexpected value!
 */
class UnexpectedValue   extends LoggableException
{
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
        (
            array
            (
                'message'     => 'Unexpected value',
                'name'        => $name,
                'value'       => self::truncate($value),
                'rules'       => $rules,
                'type'        => self::get_value_type($value)
            )
        );
    }
}
?>