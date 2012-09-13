<?PHP
namespace Exceptions\Resource\FileSystem;

class FileNotExists     extends     \Exceptions\Resource\ResourceNotExists
                        implements  FileSystemExceptionI
{
    protected $system   = self::SYSTEM;

    public function __construct($resource, $type = 'file')
    {
        parent::__construct($resource, $type);
    }
}

?>