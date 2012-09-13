<?PHP
namespace Exceptions;

/**
 * Класс для исключений
 * аспекта Runtime.
 * Исключения этого класса не попадают в журнал.
 */
class RuntimeException extends BaseException implements RuntimeExceptionI
{
}
?>