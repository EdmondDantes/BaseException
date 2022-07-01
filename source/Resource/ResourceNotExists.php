<?php declare(strict_types=1);
namespace Exceptions\Resource;

class ResourceNotExists extends ResourceException
{
    protected string $template = '{system} error: {type} is not exist. Resource: {resource}, Operation: {operation}';

    public function __construct($resource, $type = 'resource')
    {
        parent::__construct
        ([
            'resource'  => $resource,
            'operation' => 'is_'.$type,
            'type'      => $type,
            'system'    => $this->system
        ]);
    }
}