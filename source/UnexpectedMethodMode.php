<?PHP
namespace Exceptions;

/**
 * Исключение бросается, если методу
 * передан не поддерживаемый режи работы.
 * Исключение должно использоваться только для тех методов,
 * которые имеют несколько режимов работы, режим должен
 * задаваться с помощью параметра метода.
 */
class UnexpectedMethodMode  extends LoggableException
{
    /**
     * Конструктор исключения
     *
     * @param       string|array    $method         Имя метода, сгенерировавшего исключение
     *                                              или массив с параметрами конструктора
     * @param       string          $mode           Название режима (переменная, в которой он был передан)
     * @param       string|int      $value          Значение режима
     */
    public function __construct($method,
                                $mode   = null,
                                $value  = null)
    {
        if(!is_scalar($method))
        {
            parent::__construct($method);
        }
        else
        {
            parent::__construct
            (
                array
                (
                    'message' => 'Unexpected method mode',
                    'method'  => $method,
                    'mode'    => $mode,
                    'value'   => $value
                )
            );
        }
    }
}
?>