<?php declare(strict_types=1);
namespace Exceptions\Resource;

class ResourceWriteError extends ResourceException
{
    public function __construct($resource, $type = 'resource')
    {
        parent::__construct($resource, $type, 'write');
    }
}