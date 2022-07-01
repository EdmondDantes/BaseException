<?php declare(strict_types=1);
namespace Exceptions;

/**
 * The variable has unexpected value!
 */
class UnexpectedValue               extends LoggableException
{
    protected string $template      = 'Unexpected value {value} occurred in the variable {name}';

    /**
     * The variable has unexpected value!
     *
     * @param       string|array        $name           Variable name
     *                                                  or list of parameters for exception
     * @param       mixed               $value          Value
     * @param       string              $rules          Rules description
     *
     */
    public function __construct($name, $value = null, $rules = null)
    {
        if(!is_scalar($name))
        {
            parent::__construct($name);
            return;
        }

        parent::__construct
        ([
            'name'        => $name,
            'value'       => $this->toString($value),
            'message'     => $rules,
            'type'        => $this->type_info($value)
        ]);
    }
}