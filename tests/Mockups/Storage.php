<?PHP
namespace Mockups;

use Exceptions\BaseExceptionI;
use Exceptions\StorageI;

class Storage implements \Exceptions\StorageI
{
    public $Exceptions = array();

    /**
     * Метод добавляет исключение
     *
     * @param       BaseExceptionI|\Throwable      $exception      Исключение
     *
     * @return      StorageI
     */
    public function add_exception($exception)
    {
        if(($exception instanceof \Exceptions\BaseExceptionI) === false
        && ($exception instanceof \Throwable) === false )
        {
            return $this;
        }

        $this->Exceptions[] = $exception;

        return $this;
    }

    /**
     * Метод вернёт список исключений как массив.
     *
     * @return      array
     */
    public function get_storage()
    {
        return $this->Exceptions;
    }

    /**
     * Метод сбрасывает журнал исключений, если он есть.
     *
     * @return      StorageI
     */
    public function reset_storage()
    {
        $this->Exceptions = array();

        return $this;
    }
}