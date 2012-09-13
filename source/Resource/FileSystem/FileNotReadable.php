<?PHP
namespace Exceptions\Resource\FileSystem;

class FileNotReadable   extends    \Exceptions\Resource\ResourceNotReadable
                        implements FileSystemExceptionI
{
    protected $system   = self::SYSTEM;

    public function __construct($resource, $type = 'file')
    {
        parent::__construct($resource, $type);
    }
}

?>