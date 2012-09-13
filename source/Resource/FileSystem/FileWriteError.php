<?PHP
namespace Exceptions\Resource\FileSystem;

class FileWriteError    extends    \Exceptions\Resource\ResourceWriteError
                        implements FileSystemExceptionI
{
    protected $system   = self::SYSTEM;

    public function __construct($resource, $type = 'file')
    {
        parent::__construct($resource, $type);
    }
}

?>