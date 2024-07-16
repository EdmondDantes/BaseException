BaseException [![PHP Composer](https://github.com/EdmondDantes/amphp-pool/actions/workflows/php.yml/badge.svg)](https://github.com/EdmondDantes/BaseException/actions/workflows/php.yml)
=============

Base Exception Library for PHP 8.2+
(The latest version: 5.0.0)

Missions:

1. Additional structural data for exceptions.
2. Aspects for exceptions.
3. Aggregation exceptions within exceptions.
4. Registration exceptions in the global registry for logging.
5. Support for the concept of message templates.
6. Support tags for exceptions (for elastic logging as example).

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
             'var'         => $this->toString($var)
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

Registry::resetExceptionLog();

$exception      = new LoggableException('this is a loggable exception');

$log            = Registry::getExceptionLog();

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
    print_r($exception->getExceptionData());

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
catch(BaseException $myException)
{
    $myException->appendData(['browser' => get_browser()]);

    // and throw exception on...
    throw $myException;
}

```

## Inheriting from the BaseException

```php
class ClassNotExist  extends BaseException
{
    // This exception will be logged
    protected bool $isLoggable = true;

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
    protected bool $isFatal    = true;
}
```

## Debug data

```php
class MyException  extends BaseException
{
    public function __construct($object)
    {
        $this->setDebugData($object);
        parent::__construct('its too bad!');
    }
}
```

[Full list here][1].

[1]: docs/01-Overview.md
