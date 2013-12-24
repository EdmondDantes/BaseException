<?PHP
namespace Exceptions\Resource;

class ResourceWriteError extends ResourceException
{
    public function __construct($resource, $type = 'resource')
    {
        parent::__construct($resource, $type, 'write');
    }
}