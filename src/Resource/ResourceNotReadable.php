<?php declare(strict_types=1);
namespace Exceptions\Resource;

class ResourceNotReadable extends ResourceException
{
    public function __construct($resource, $type = 'resource')
    {
        parent::__construct($resource, $type, 'readable');
    }
}