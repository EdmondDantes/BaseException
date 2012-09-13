<?PHP
namespace Exceptions;

/**
 * Специальное исключение, для случая необработанного исключения.
 * Используется только для логировния.
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