<?PHP
namespace Exceptions;

/**
 * StorageI - хранилище для исключений.
 *
 */
interface StorageI
{
    /**
     * Метод добавляет исключение
     *
     * @param       BaseExceptionI|\Exception      $exception      Исключение
     *
     * @return      StorageI
     */
    public function add_exception($exception);

    /**
     * Метод вернёт список исключений как массив.
     *
     * @return      array
     */
    public function get_storage();

    /**
     * Метод сбрасывает журнал исключений, если он есть.
     *
     * @return      StorageI
     */
    public function reset_storage();
}

?>