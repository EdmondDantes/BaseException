<?php declare(strict_types=1);
namespace Exceptions\Resource\FileSystem;

use Exceptions\Resource\ResourceReadError;

class FileReadError     extends    ResourceReadError
                        implements FileSystemExceptionI
{
    protected string $system   = self::SYSTEM;

    public function __construct($resource, $type = 'file')
    {
        parent::__construct($resource, $type);
    }
}