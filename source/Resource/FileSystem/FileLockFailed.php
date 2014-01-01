<?PHP
namespace Exceptions\Resource\FileSystem;

use Exceptions\Resource\ResourceLockFailed;

class FileLockFailed    extends    ResourceLockFailed
                        implements FileSystemExceptionI
{
    protected $system   = self::SYSTEM;

    public function __construct($resource, $type = 'file')
    {
        parent::__construct($resource, $type);
    }
}