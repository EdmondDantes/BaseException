BaseException
=============

Base Exception Library for PHP 5.4+

Missions:

1. Additional structural data for exceptions.
2. Aspects for exceptions.
3. Aggregation exceptions within exceptions.
4. Registration exceptions in the global registry for logging.

**And most importantly: make it all easy and simple ;)**

# Overview

## BaseException::__construct()

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

Container-Exception:

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
        // out "test"
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

## Inheriting from the BaseException

```php
class ClassNotExist  extends BaseException
{
    // This exception will be logged
    protected $is_loggable = true;

    /**
     * ClassNotExist
     *
     * @param       string      $class         Class name
     */
    public function __construct($class)
    {
        parent::__construct
        (
            array
            (
                'message' => "Сlass '$class' does not exist",
                'class'   => $class
            )
        );
    }
}
```

## FatalException

```php
class MyFatalException  extends BaseException
{
    // This exception has aspect: "fatal"
    protected $is_fatal    = true;
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