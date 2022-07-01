<?php declare(strict_types=1);
namespace Exceptions;

/**
 * Object can't be unserialized!
 */
class UnSerializeException          extends LoggableException
{
    protected string $template      = 'Unserialize process was failed (type: {type}, node: {node})';

    /**
     * Object can't be serialized!
     *
     * @param       string|array        $reason         Reason of error
     * @param       string              $type           Type of serialize
     * @param       mixed               $node           The node which must have been serialized
     */
    public function __construct($reason, $type = 'phpserialize', $node = null)
    {
        if(!is_string($reason))
        {
            parent::__construct($reason);
            return;
        }

        parent::__construct
        ([
            'message'       => $reason,
            'type'          => $type,
            'node'          => $this->typeInfo($node)
        ]);
    }
}