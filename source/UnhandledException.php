<?PHP
namespace Exceptions;

/**
 * Special exception, which is used to mark an unhandled exception.
 * Is used in the `Registry`.
 */
class UnhandledException extends LoggableException
{
    protected $template = 'Unhandled Exception {type} occurred';

    /**
     * @param \Exception|BaseExceptionI $exception
     */
    public function __construct(\Exception $exception)
    {
        parent::__construct
        ([
            'message'   => 'Unhandled Exception',
            'type'      => get_class($exception),
            'source'    => $this->get_source_for($exception),
            'previous'  => $exception
        ]);
    }
}