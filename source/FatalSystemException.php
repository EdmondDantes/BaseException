<?PHP
namespace Exceptions;

/**
 * Фатальное исключение системы
 * для Системных ошибок.
 */
class FatalSystemException extends FatalException implements SystemExceptionI
{
}
?>