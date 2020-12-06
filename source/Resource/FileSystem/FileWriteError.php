<?PHP
namespace Exceptions\Resource\FileSystem;

use Exceptions\Resource\ResourceWriteError;

class FileWriteError    extends    ResourceWriteError
                        implements FileSystemExceptionI
{
    protected string $system   = self::SYSTEM;

    public function __construct($resource, $type = 'file')
    {
        parent::__construct($resource, $type);
    }
}