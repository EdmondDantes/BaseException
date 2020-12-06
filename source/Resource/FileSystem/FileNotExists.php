<?PHP
namespace Exceptions\Resource\FileSystem;

use Exceptions\Resource\ResourceNotExists;

class FileNotExists     extends     ResourceNotExists
                        implements  FileSystemExceptionI
{
    protected string $system   = self::SYSTEM;

    public function __construct($resource, $type = 'file')
    {
        parent::__construct($resource, $type);
    }
}