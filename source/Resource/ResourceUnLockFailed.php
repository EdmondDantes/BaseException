<?PHP
namespace Exceptions\Resource;

class ResourceUnLockFailed extends ResourceException
{
    public function __construct($resource, $type = 'resource')
    {
        parent::__construct($resource, $type, 'unlock');
    }
}