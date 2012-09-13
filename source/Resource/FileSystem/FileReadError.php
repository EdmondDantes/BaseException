<?PHP
namespace Exceptions\Resource\FileSystem;

class FileReadError     extends   \Exceptions\Resource\ResourceReadError
                        implements FileSystemExceptionI
{
    protected $system   = self::SYSTEM;

    public function __construct($resource, $type = 'file')
    {
        parent::__construct($resource, $type);
    }
}

?>