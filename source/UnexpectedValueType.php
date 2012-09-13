<?PHP
namespace Exceptions;

/**
 * Исключение бросается, если переменная
 * имеет не тот тип, который ожидался
 */
class UnexpectedValueType   extends LoggableException
{
    /**
     * Конструктор исключения
     *
     * @param       string|array        $name           Имя переменной
     *                                                  или массив с параметрами конструктора
     * @param       mixed               $value          Значение, которое было получено
     * @param       string              $expected       Тип, который ожидался
     */
    public function __construct($name,
                                $value      = null,
                                $expected   = null)
    {
        if(!is_scalar($name))
        {
            parent::__construct($name);
            return;
        }

        parent::__construct
        (
            array
            (
                'message'     => 'Unexpected value type',
                'name'        => $name,
                'value'       => self::get_value_type($value),
                'expected'    => $expected
            )
        );
    }
}
?>