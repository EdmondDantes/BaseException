<?php declare(strict_types=1);
namespace Exceptions;

trait HelperT
{
    /**
     * The method defines the source of the exception.
     *
     * @param       \Throwable $e
     * @param       boolean    $isString
     *
     * @return      array|string
     */
    final protected function getSourceFor(\Throwable $e, bool $isString = false): array|string
    {
        $res                    = $e->getTrace()[0];

        if($isString)
        {
            return  ($res['class'] ?? $res['file'] . ':' . $res['line']).
                    ($res['type'] ?? '.').
                    ($res['function'] ?? '{}');
        }

        return
        [
            'source'            => $res['class'] ?? $res['file'] . ':' . $res['line'],
            'type'              => $res['type'] ?? '.',
            'function'          => $res['function'] ?? '{}',
        ];
    }

    /**
     * The method returns a type of $value or class name.
     *
     * It must use in order to exclude objects from the exception.
     *
     * @param           mixed           $value      value
     *
     * @return          string
     */
    final protected function getValueType(mixed $value): string
    {
        if(is_bool($value))
        {
            return $value ? 'TRUE' : 'FALSE';
        }
        elseif(is_object($value))
        {
            return get_debug_type($value);
        }
        elseif(is_null($value))
        {
            return 'NULL';
        }
        elseif(is_string($value))
        {
            return 'STRING';
        }
        elseif(is_int($value))
        {
            return 'INTEGER';
        }
        elseif(is_float($value))
        // is_double some
        {
            return 'DOUBLE';
        }
        elseif(is_array($value))
        {
            return 'ARRAY('.count($value).')';
        }
        elseif(is_resource($value))
        {
            $type           = get_resource_type($value);
            $meta           = '';
            if($type === 'stream' && is_array($meta = stream_get_meta_data($value)))
            {
                // array keys normalize
                $meta       = array_merge
                (
                    ['stream_type' => '', 'wrapper_type' => '', 'mode' => '', 'uri' => ''],
                    $meta
                );
                $meta       = " ({$meta['stream_type']}, {$meta['wrapper_type']}, {$meta['mode']}) {$meta['uri']}";
            }

            return 'RESOURCE: '.$type.$meta;
        }
        else
        {
            return get_debug_type($value);
        }
    }

    /**
     * The method convert $value to string.
     *
     * @param       mixed   $value    Value
     * @param       boolean $isQuoted If result has been quoted?
     * @param       int     $arrayMax Max count items of array
     *
     * @return      string
     */
    protected function toString(mixed $value, bool $isQuoted = false, int $arrayMax = 5): string
    {
        // truncate data
        if(is_string($value) && strlen($value) > 255)
        {
            $value          = substr($value, 0, 255).'…';
        }
        elseif(is_bool($value))
        {
            $value          = $value ? 'TRUE' : 'FALSE';
            $isQuoted       = false;
        }
        elseif(is_null($value))
        {
            $value          = 'NULL';
            $isQuoted       = false;
        }
        elseif(is_scalar($value))
        {
            $value          = (string)$value;
        }
        elseif(is_array($value))
        {
            $result         = [];

            foreach(array_slice($value, 0, $arrayMax, true) as $key => $item)
            {
                if(is_scalar($item))
                {
                    $result[] = $this->toString($key, false).':'.$this->toString($item, $isQuoted);
                }
                else
                {
                    $result[] = $this->toString($key, false).':'.$this->getValueType($item);
                }
            }

            if(count($value) > $arrayMax)
            {
                $value      = count($value).'['.implode(', ', $result).']';
            }
            else
            {
                $value      = '['.implode(', ', $result).']';
            }

            $isQuoted      = false;
        }
        else
        {
            $value          = $this->getValueType($value);
            $isQuoted       = false;
        }

        if($isQuoted)
        {
            $value          = '\''.$value.'\'';
        }

        return $value;
    }
}