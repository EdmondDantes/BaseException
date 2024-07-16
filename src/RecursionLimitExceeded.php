<?php declare(strict_types=1);
namespace IfCastle\Exceptions;

/**
 * Reached a maximum depth of recursion
 */
class RecursionLimitExceeded extends LoggableException
{
    protected string $template         = 'Recursion limit exceeded: {limit}';

    /**
     * Reached a maximum depth of recursion
     *
     * @param       int|array           $limit          maximum depth
     */
    public function __construct($limit)
    {
        if(!is_scalar($limit))
        {
            parent::__construct($limit);
        }
        else
        {
            parent::__construct(['limit' => $limit]);
        }
    }
}