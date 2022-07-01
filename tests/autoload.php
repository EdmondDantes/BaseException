<?php

declare(strict_types=1);

define('UNIT_TEST_ROOT', dirname(__FILE__));

require_once '../vendor/autoload.php';

set_include_path
(
    dirname(__FILE__).PATH_SEPARATOR.
    dirname(__DIR__).'/src'.PATH_SEPARATOR.
    dirname(__FILE__).'/src'.PATH_SEPARATOR.
    get_include_path()
);

spl_autoload_register(function($class)
{
    if(str_starts_with($class, 'Exceptions'))
    {
        $class = substr($class, strlen('Exceptions') + 1);
    }

    $class = '/'.str_replace('\\', '/', $class).'.php';

    foreach(explode(PATH_SEPARATOR, get_include_path()) as $path)
    {
        if(is_file($path.$class))
        {
            include_once $path.$class;
            return;
        }
    }
});