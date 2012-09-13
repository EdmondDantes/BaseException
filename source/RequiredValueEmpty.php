<?PHP
namespace Exceptions;

/**
 * If required value is empty
 */
class RequiredValueEmpty extends LoggableException
{
    /**
     * If required value is empty
     *
     * @param       string|array        $name           Variable name
     *                                                  or array with parameters.
     * @param       string              $expected       Excepted type
     */
    public function __construct($name, $expected = null)
    {
        if(!is_scalar($name))
        {
            parent::__construct($name);
        }
        else
        {
            parent::__construct
            (
                array
                (
                    'message'     => 'Required value empty',
                    'name'        => $name,
                    'expected'    => $expected
                )
            );
        }
    }
}
?>