<?PHP
namespace Exceptions\Resource\FileSystem;

use \Exceptions\Resource\ResourceOpenError;

class FileOpenError     extends    ResourceOpenError
                        implements FileSystemExceptionI
{
    protected $system   = self::SYSTEM;

    public function __construct($resource, $type = 'file')
    {
        parent::__construct($resource, $type);
    }
}