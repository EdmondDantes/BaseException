<?PHP
namespace Exceptions;

/**
 * Special exception, which is used to mark an unhandled exception.
 * Is used in the `Registry`.
 */
class UnhandledException extends LoggableException
{
    public function __construct(\Exception $exception)
    {
        parent::__construct
        ([
            'message'   => 'Unhandled Exception',
            'type'      => get_class($exception),
            'source'    => self::get_source_for($exception),
            'previous'  => $exception
        ]);
    }
}
?>