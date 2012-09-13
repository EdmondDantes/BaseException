<?PHP
namespace Exceptions;

/**
 * Исключение бросается, если необходимая
 * переменная не задана или пуста
 */
class RequiredValueEmpty extends LoggableException
{
    /**
     * Конструктор исключения
     *
     * @param       string|array        $name           Имя переменной
     *                                                  или массив с параметрами конструктора
     * @param       string              $expected       Тип, который ожидался
     */
    public function __construct($name, $expected = null)
    {
        if(!is_scalar($name))
        {
            parent::__construct($name);
        }
        else
        {
            parent::__construct
            (
                array
                (
                    'message'     => 'Required value empty',
                    'name'        => $name,
                    'expected'    => $expected
                )
            );
        }
    }
}
?>