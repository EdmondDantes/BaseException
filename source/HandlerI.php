<?PHP
namespace Exceptions;

interface HandlerI
{
    /**
     * Exception handler
     *
     * @param       \Exception|BaseExceptionI   $exception
     *
     * @return      void
     */
    public function exception_handler($exception);
}