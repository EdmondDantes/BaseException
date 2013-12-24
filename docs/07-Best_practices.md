# Best practices

## Пример явного логирования ошибки аспекта Runtime

```php

        //
        // Redefine error_handler for preg_match_all().
        //
        $old_handler        = set_error_handler(function ($errno, $errstr)
        {
            new RuntimeException
            ([
                'message'   => 'Preg error: '.$errstr,
                'code'      => $errno,
                'preg'      => $this->rule
            ]);
        });
        $matches = [];
        $res     = preg_match_all($this->rule, $query, $matches);
        set_error_handler($old_handler);

        if(!$res)
        {
            return false;
        }
```
