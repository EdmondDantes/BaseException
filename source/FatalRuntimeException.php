<?PHP
namespace Exceptions;

/**
 * Фатальное исключение системы
 * для Runtime ошибок.
 */
class FatalRuntimeException extends FatalException implements RuntimeExceptionI
{
}
?>