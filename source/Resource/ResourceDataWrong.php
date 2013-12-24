<?PHP
namespace Exceptions\Resource;

class ResourceDataWrong extends ResourceException
{
    protected $template =
    '{system} error: data is wrong (expected {format}) for resource {resource}';

    public function __construct($resource, $type = 'resource', $format = 'format')
    {
        parent::__construct
        ([
            'resource'  => $resource,
            'operation' => 'format:'.$format,
            'format'    => $format,
            'system'    => $this->system
        ]);
    }
}