<?php declare(strict_types=1);
namespace IfCastle\Exceptions\Resource\FileSystem;

use Exceptions\Resource\ResourceLockFailed;

class FileLockFailed    extends    ResourceLockFailed
                        implements FileSystemExceptionInterface
{
    protected string $system   = self::SYSTEM;

    public function __construct($resource, $type = 'file')
    {
        parent::__construct($resource, $type);
    }
}