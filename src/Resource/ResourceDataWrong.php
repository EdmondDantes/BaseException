<?php declare(strict_types=1);
namespace Exceptions\Resource;

class ResourceDataWrong extends ResourceException
{
    protected string $template = '{system} error: data is wrong (expected {format}) for resource {resource}';

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