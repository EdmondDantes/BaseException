<?PHP
namespace Exceptions\Resource\FileSystem;

class FileUnLockFailed  extends   \Exceptions\Resource\ResourceUnLockFailed
                        implements FileSystemExceptionI
{
    protected $system   = self::SYSTEM;

    public function __construct($resource, $type = 'file')
    {
        parent::__construct($resource, $type);
    }
}

?>