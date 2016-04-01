<?PHP
namespace Exceptions;

interface HandlerI
{
    /**
     * Exception handler
     *
     * @param       \Throwable|BaseExceptionI   $exception
     *
     * @return      void
     */
    public function exception_handler($exception);
}