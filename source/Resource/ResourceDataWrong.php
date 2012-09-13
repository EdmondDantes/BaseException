<?PHP
namespace Exceptions\Resource;

class ResourceDataWrong extends ResourceException
{
    public function __construct($resource, $type = 'resource', $format = 'format')
    {
        parent::__construct
        ([
            'message'   => $this->system.' error: data wrong (expected "'.$format.'")',
            'resource'  => $resource,
            'operation' => 'format:'.$format,
            'format'    => $format,
            'system'    => $this->system
        ]);
    }
}

?>