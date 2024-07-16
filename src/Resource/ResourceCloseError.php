<?php declare(strict_types=1);
namespace IfCastle\Exceptions\Resource;

class ResourceCloseError extends ResourceException
{
    public function __construct($resource, $type = 'resource')
    {
        parent::__construct($resource, $type, 'close');
    }
}