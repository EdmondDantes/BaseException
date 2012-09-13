<?PHP
namespace Exceptions;

/**
 * Исключение бросается, если переменная
 * имеет недопустимое значение.
 */
class UnexpectedValue   extends LoggableException
{
    /**
     * Конструктор исключения
     *
     * @param       string|array        $name           Имя переменной
     *                                                  или массив с параметрами конструктора
     * @param       mixed               $value          Значение, которое было получено
     * @param       string              $rules          Указание на свод правил,
     *                                                  который был нарушен.
     */
    public function __construct($name, $value = null, $rules = null)
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
                'message'     => 'Unexpected value',
                'name'        => $name,
                'value'       => self::truncate($value),
                'rules'       => $rules,
                'type'        => self::get_value_type($value)
            )
        );
    }
}
?>