<?PHP
namespace Exceptions\Resource;

class ResourceLockFailed extends ResourceException
{
    public function __construct($resource, $type = 'resource')
    {
        parent::__construct($resource, $type, 'lock');
    }
}