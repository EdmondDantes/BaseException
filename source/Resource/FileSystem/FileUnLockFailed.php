<?PHP
namespace Exceptions\Resource\FileSystem;

use \Exceptions\Resource\ResourceUnLockFailed;

class FileUnLockFailed  extends    ResourceUnLockFailed
                        implements FileSystemExceptionI
{
    protected $system   = self::SYSTEM;

    public function __construct($resource, $type = 'file')
    {
        parent::__construct($resource, $type);
    }
}