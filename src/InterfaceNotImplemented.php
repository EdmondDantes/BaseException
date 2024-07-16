<?php declare(strict_types=1);
namespace IfCastle\Exceptions;

/**
 * If class not implemented required interface
 */
class InterfaceNotImplemented       extends LoggableException
{
    protected string $template      = 'Class {class} does not implement interface {interface}';

    /**
     * Constructor for InterfaceNotImplemented
     *
     * @param       string|array|object     $class         Class name
     * @param       string                  $interface     Required interface
     */
    public function __construct($class, $interface)
    {
        if(is_array($class))
        {
            parent::__construct($class);
        }
        else
        {
            parent::__construct
            ([
                'class'     => $this->typeInfo($class),
                'interface' => $interface
            ]);
        }
    }
}