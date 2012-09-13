<?PHP
namespace Exceptions\Resource\FileSystem;

class FileOpenError     extends   \Exceptions\Resource\ResourceOpenError
                        implements FileSystemExceptionI
{
    protected $system   = self::SYSTEM;

    public function __construct($resource, $type = 'file')
    {
        parent::__construct($resource, $type);
    }
}

?>