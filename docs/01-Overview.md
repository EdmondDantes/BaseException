# Overview

# BaseException::__construct()

Обычное использование:

```php
    throw new BaseException('message', 0, $previous);
```

Дополнительные данные:

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

Исключение-контейнер:

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

Исключение контейнер с журнализированием:

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

## FatalException

## Debug data

## BaseException static methods

## Registry

## StorageI
