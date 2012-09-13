<?PHP
namespace Exceptions\Resource\FileSystem;

class FileCloseError    extends   \Exceptions\Resource\ResourceCloseError
                        implements FileSystemExceptionI
{
    protected $system   = self::SYSTEM;

    public function __construct($resource, $type = 'file')
    {
        parent::__construct($resource, $type);
    }
}

?>