<?PHP
namespace Exceptions\Resource;

class ResourceOpenError extends ResourceException
{
    public function __construct($resource, $type = 'resource')
    {
        parent::__construct($resource, $type, 'open');
    }
}