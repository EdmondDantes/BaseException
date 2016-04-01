<?PHP
namespace Exceptions;

/**
 * Special exception, which is used to mark an unhandled exception.
 * Is used in the `Registry`.
 */
class UnhandledException extends LoggableException
{
    protected $template = 'Unhandled Exception {type} occurred in the {source}';

    /**
     * @param \Throwable|BaseExceptionI $exception
     */
    public function __construct(\Throwable $exception)
    {
        parent::__construct
        ([
            'type'      => $this->type_info($exception),
            'source'    => $this->get_source_for($exception),
            'previous'  => $exception
        ]);
    }
}