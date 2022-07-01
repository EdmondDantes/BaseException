<?php declare(strict_types=1);
namespace Exceptions\Resource\FileSystem;

use Exceptions\Resource\ResourceNotReadable;

class FileNotReadable   extends    ResourceNotReadable
                        implements FileSystemExceptionI
{
    protected string $system   = self::SYSTEM;

    public function __construct($resource, $type = 'file')
    {
        parent::__construct($resource, $type);
    }
}