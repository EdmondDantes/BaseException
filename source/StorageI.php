<?PHP
namespace Exceptions;

/**
 * StorageI - Interface for exception storage.
 *
 */
interface StorageI
{
    /**
     * Add exception into storage
     *
     * @param       BaseExceptionI|\Exception      $exception      Exception
     *
     * @return      StorageI
     */
    public function add_exception($exception);

    /**
     * Returns list of exceptions
     *
     * @return      array
     */
    public function get_storage();

    /**
     * Reset storage
     *
     * @return      StorageI
     */
    public function reset_storage();
}

?>