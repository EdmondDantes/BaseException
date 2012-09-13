<?PHP
namespace Exceptions\Resource;

class ResourceNotWriteable extends ResourceException
{
    public function __construct($resource, $type = 'resource')
    {
        parent::__construct($resource, $type, 'is_writeable');
    }
}

?>