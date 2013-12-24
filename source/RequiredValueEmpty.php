<?PHP
namespace Exceptions;

/**
 * If required value is empty
 */
class RequiredValueEmpty        extends LoggableException
{
    protected $template         = 'The Required value {name} is empty ({expected})';

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
            parent::__construct(['name' => $name, 'expected' => $expected]);
        }
    }
}