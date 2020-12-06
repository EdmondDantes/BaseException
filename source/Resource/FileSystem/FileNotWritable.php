<?PHP
namespace Exceptions\Resource\FileSystem;

use Exceptions\Resource\ResourceNotWritable;

class FileNotWritable  extends     ResourceNotWritable
                        implements FileSystemExceptionI
{
    protected string $system   = self::SYSTEM;

    public function __construct($resource, $type = 'file')
    {
        parent::__construct($resource, $type);
    }
}