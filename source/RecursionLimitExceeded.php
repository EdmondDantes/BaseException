<?PHP
namespace Exceptions;

/**
 * Исключение бросается при достижении максимальной
 * глубины рекурсионного вызова метода или фукнции
 */
class RecursionLimitExceeded extends LoggableException
{
    /**
     * Конструктор исключения
     *
     * @param       int|array           $limit          Максимальная глубина рекурсии
     *                                                  или массив с параметрами конструктора
     */
    public function __construct($limit)
    {
        if(!is_scalar($limit))
        {
            parent::__construct($limit);
        }
        else
        {
            parent::__construct(array('message' => 'Recursion limit exceeded', 'limit' => $limit));
        }
    }
}
?>