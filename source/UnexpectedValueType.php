<?php declare(strict_types=1);
namespace Exceptions;

/**
 * Value of variable has unexpected type.
 */
class UnexpectedValueType           extends LoggableException
{
    protected string $template      = 'Unexpected type occurred for the value {name} and type {type}. Expected {expected}';

    /**
     * Value of variable has unexpected type.
     *
     * @param       string|array        $name           Variable name
     *                                                  or list of parameters for exception
     * @param       mixed               $value          Value
     * @param       string              $expected       Excepted type
     */
    public function __construct($name,
                                $value      = null,
                                $expected   = null)
    {
        if(!is_scalar($name))
        {
            parent::__construct($name);
            return;
        }

        parent::__construct
        ([
            'name'        => $name,
            'type'        => $this->type_info($value),
            'expected'    => $expected
        ]);
    }
}