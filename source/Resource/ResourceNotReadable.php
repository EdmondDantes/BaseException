<?PHP
namespace Exceptions\Resource;

class ResourceNotReadable extends ResourceException
{
    public function __construct($resource, $type = 'resource')
    {
        parent::__construct($resource, $type, 'readable');
    }
}