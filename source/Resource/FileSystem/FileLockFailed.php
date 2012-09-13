<?PHP
namespace Exceptions\Resource\FileSystem;

class FileLockFailed    extends   \Exceptions\Resource\ResourceLockFailed
                        implements FileSystemExceptionI
{
    protected $system   = self::SYSTEM;

    public function __construct($resource, $type = 'file')
    {
        parent::__construct($resource, $type);
    }
}

?>