<?PHP
namespace Exceptions\Resource;

class ResourceReadError extends ResourceException
{
    public function __construct($resource, $type = 'resource')
    {
        parent::__construct($resource, $type, 'read');
    }
}

?>