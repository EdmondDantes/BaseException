<?PHP
namespace Exceptions;

/**
 * Исключение бросается,
 * если метод нельзя вызывать в данной ситуации
 * (обычно из-за ошибки программиста)
 */
class MethodNotCallable extends LogicalException
{
    /**
     * Конструктор исключения
     *
     * @param       string|array        $method         Метод, в котором было брошено исключение
     *                                                  или массив с параметрами конструктора
     * @param       string              $message        Сообщение об ошибке
     */
    public function __construct($method, $message = null)
    {
        if(!is_scalar($method))
        {
            parent::__construct($method);
        }
        else
        {
            parent::__construct(array('method'  => $method,
                                      'message' => $message));
        }
    }
}
?>
