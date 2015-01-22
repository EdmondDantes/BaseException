<?PHP
namespace Exceptions;

interface SaveHandlerI
{
    /**
     * Save handler method
     *
     * @param       array                            $exceptions
     * @param       callable                         $reset_log
     * @param       array|\ArrayAccess               $logger_options
     * @param       array|\ArrayAccess               $debug_options
     *
     * @return      void
     */
    public function save_exceptions(array $exceptions , callable $reset_log, $logger_options = [], $debug_options = []);
}