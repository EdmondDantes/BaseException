<?PHP
namespace Exceptions\Resource\FileSystem;

class FileNotWriteable  extends     \Exceptions\Resource\ResourceNotWriteable
                        implements FileSystemExceptionI
{
    protected $system   = self::SYSTEM;

    public function __construct($resource, $type = 'file')
    {
        parent::__construct($resource, $type);
    }
}

?>