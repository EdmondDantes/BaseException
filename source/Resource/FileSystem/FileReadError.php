<?PHP
namespace Exceptions\Resource\FileSystem;

use \Exceptions\Resource\ResourceReadError;

class FileReadError     extends    ResourceReadError
                        implements FileSystemExceptionI
{
    protected $system   = self::SYSTEM;

    public function __construct($resource, $type = 'file')
    {
        parent::__construct($resource, $type);
    }
}