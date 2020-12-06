BaseException [![Build Status](https://secure.travis-ci.org/EdmondDantes/BaseException.png)](http://travis-ci.org/EdmondDantes/BaseException)
=============

Base Exception Library for PHP 8.0+
(The latest version: 4.0.0)

Missions:

1. Additional structural data for exceptions.
2. Aspects for exceptions.
3. Aggregation exceptions within exceptions.
4. Registration exceptions in the global registry for logging.
5. Support for the concept of message templates.

**And most importantly: make it all easy and simple ;)**

# Overview

## Templates for the error message

```php
class MyException extends \Exceptions\BaseException
{
    protected string $template      = 'The template error message with {var}';

    public function __construct($var)
    {
        parent::__construct
        ([
             'var'         => $this->to_string($var)
         ]);
    }
}

$exception = new MyException('string');

// should be printed: The template error message with 'string'
echo $exception->getMessage();
```

## Independent logging exceptions (Exceptions Registry)

```php

use \Exceptions\Registry;
use \Exceptions\LoggableException;

Registry::reset_exception_log();

$exception      = new LoggableException('this is a loggable exception');

$log            = Registry::get_exception_log();

if($log[0] === $exception)
{
    echo 'this is loggable $exception';
}

```

## Support of the exception context parameters

The basic use:

```php
    throw new BaseException('message', 0, $previous);
```

List of parameters:

```php
    // use array()
    $exception = new BaseException
    ([
        'message'     => 'message',
        'code'        => 0,
        'previous'    => $previous,
        'mydata'      => [1,2,3]
    ]);

    ...

    // print_r([1,2,3]);
    print_r($exception->get_data());

```

## Exception Container

```php

    try
    {
        try
        {
            throw new \Exception('test');
        }
        catch(\Exception $e)
        {
            // inherits data Exception
            throw new BaseException($e);
        }
    }
    catch(BaseException $exception)
    {
        // should be printed: "test"
        echo $exception->getMessage();
    }

```

The container is used to change the flag `is_loggable`:

```php

    try
    {
        try
        {
            // not loggable exception!
            throw new BaseException('test');
        }
        catch(\Exception $e)
        {
            // log BaseException, but don't log LoggableException
            throw new LoggableException($e);
        }
    }
    catch(LoggableException $exception)
    {
        // echo: "true"
        if($exception->getPrevious() === $e)
        {
            echo 'true';
        }
    }

```

## Appends parameters after the exception has been thrown

```php

try
{
    dispatch_current_url();
}
catch(BaseException $my_exception)
{
    $my_exception->append_data(['browser' => get_browser()]);

    // and throw exception on...
    throw $my_exception;
}

```

## Inheriting from the BaseException

```php
class ClassNotExist  extends BaseException
{
    // This exception will be logged
    protected bool $is_loggable = true;

    /**
     * ClassNotExist
     *
     * @param       string      $class         Class name
     */
    public function __construct(string $class)
    {
        parent::__construct
        ([
             'template'    => 'Сlass {class} does not exist',
             'class'       => $class
        ]);
    }
}
```

## FatalException

```php
class MyFatalException  extends BaseException
{
    // This exception has aspect: "fatal"
    protected bool $is_fatal    = true;
}
```

## Debug data

```php
class MyException  extends BaseException
{
    public function __construct($object)
    {
        $this->set_debug_data($object);
        parent::__construct('its too bad!');
    }
}
```

[Full list here][1].

[1]: docs/01-Overview.md
