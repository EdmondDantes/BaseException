<?PHP
namespace Exceptions;

/**
 * Value of variable has unexpected type.
 */
class UnexpectedValueType   extends LoggableException
{
    /**
     * Value of variable has unexpected type.
     *
     * @param       string|array        $name           Variable name
     *                                                  or list of parameters for exception
     * @param       mixed               $value          Value
     * @param       string              $expected       Excepted type
     */
    public function __construct($name,
                                $value      = null,
                                $expected   = null)
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
                'message'     => 'Unexpected value type',
                'name'        => $name,
                'value'       => self::get_value_type($value),
                'expected'    => $expected
            )
        );
    }
}
?>