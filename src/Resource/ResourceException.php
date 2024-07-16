<?php declare(strict_types=1);
namespace IfCastle\Exceptions\Resource;

use Exceptions\SystemException;

/**
 * The basic class for exceptions related to resources.
 *
 * Child classes must define the property $system to identify the system
 */
class ResourceException extends SystemException
{
    protected string $template  =
        '{system} error: operation {operation} for the resource {resource} ({type}) is failed';

    protected string $system    = 'undefined';

    /**
     * @param       string|object|array $resource
     * @param       string              $operation
     * @param       string              $type
     */
    public function __construct($resource, $type = '', $operation = '')
    {
        if(!is_scalar($resource))
        {
            parent::__construct($resource);
        }
        else
        {
            parent::__construct
            ([
                'message'   => $this->resource_system().' error: operation "'.$operation.'" failed',
                'resource'  => $resource,
                'operation' => $operation,
                'type'      => $type,
                'system'    => $this->resource_system()
            ]);
        }
    }

    /**
     * Method return Resource
     *
     * @return mixed
     */
    public function resource()
    {
        return $this->data['resource'] ?? '';
    }

    /**
     * Method return system of Resource
     * @return string
     */
    public function resource_system()
    {
        return $this->system;
    }

    /**
     * Method return type of Resource
     *
     * @return string
     */
    public function resource_type()
    {
        return $this->data['type'] ?? '';
    }

    /**
     * Method return operation
     *
     * @return string
     */
    public function resource_operation()
    {
        return $this->data['operation'] ?? '';
    }
}