<?PHP
namespace Exceptions;

/**
 * Reached a maximum depth of recursion
 */
class RecursionLimitExceeded extends LoggableException
{
    /**
     * Reached a maximum depth of recursion
     *
     * @param       int|array           $limit          maximum depth
     */
    public function __construct($limit)
    {
        if(!is_scalar($limit))
        {
            parent::__construct($limit);
        }
        else
        {
            parent::__construct(array('message' => 'Recursion limit exceeded', 'limit' => $limit));
        }
    }
}
?>