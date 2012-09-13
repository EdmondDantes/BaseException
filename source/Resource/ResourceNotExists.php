<?PHP
namespace Exceptions\Resource;

class ResourceNotExists extends ResourceException
{
    public function __construct($resource, $type = 'resource')
    {
        parent::__construct
        ([
            'message'   => $this->system.' Exception: '.$type.' not exists',
            'resource'  => $resource,
            'operation' => 'is_'.$type,
            'type'      => $type,
            'system'    => $this->system
        ]);
    }
}

?>