<?PHP
namespace Exceptions;

/**
 * If class not exists or not loaded with autoload.
 */
class ClassNotExist  extends LoggableException
{
    protected $template     = 'The class {class} does not exist';

    /**
     * ClassNotExist
     *
     * @param       string|array    $class         Class name
     */
    public function __construct($class)
    {
        if(!is_scalar($class))
        {
            parent::__construct($class);
        }
        else
        {
            parent::__construct(['class'   => $class ]);
        }
    }
}