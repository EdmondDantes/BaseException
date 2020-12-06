<?PHP
namespace Exceptions;

trait HelperT
{
    /**
     * The method defines the source of the exception.
     *
     * @param       \Throwable      $e
     * @param       boolean         $is_string
     *
     * @return      array|string
     */
    final protected function get_source_for(\Throwable $e, $is_string = false)
    {
        $res                    = $e->getTrace()[0];

        if($is_string)
        {
            return  (isset($res['class'])      ? $res['class']     : $res['file'].':'.$res['line']).
                    (isset($res['type'])       ? $res['type']      : '.').
                    (isset($res['function'])   ? $res['function']  : '{}');
        }

        return
        [
            'source'    => isset($res['class'])      ? $res['class']     : $res['file'].':'.$res['line'],
            'type'      => isset($res['type'])       ? $res['type']      : '.',
            'function'  => isset($res['function'])   ? $res['function']  : '{}',
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
    final protected function get_value_type($value)
    {
        if(is_bool($value))
        {
            return $value ? 'TRUE' : 'FALSE';
        }
        elseif(is_object($value))
        {
            return get_class($value);
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
            return gettype($value);
        }
    }

    /**
     * The method convert $value to string.
     *
     * @param       mixed       $value          Value
     * @param       boolean     $is_quoted      If result has been quoted?
     * @param       int         $array_max      Max count items of array
     *
     * @return      string
     */
    protected function to_string(mixed $value, bool $is_quoted = false, int $array_max = 5)
    {
        // truncate data
        if(is_string($value) && strlen($value) > 255)
        {
            $value          = substr($value, 0, 255).'…';
        }
        elseif(is_bool($value))
        {
            $value          = $value ? 'TRUE' : 'FALSE';
            $is_quoted      = false;
        }
        elseif(is_null($value))
        {
            $value          = 'NULL';
            $is_quoted      = false;
        }
        elseif(is_scalar($value))
        {
            $value          = (string)$value;
        }
        elseif(is_array($value))
        {
            $result         = [];

            foreach(array_slice($value, 0, $array_max, true) as $key => $item)
            {
                if(is_scalar($item))
                {
                    $result[] = $this->to_string($key, false).':'.$this->to_string($item, $is_quoted);
                }
                else
                {
                    $result[] = $this->to_string($key, false).':'.$this->get_value_type($item);
                }
            }

            if(count($value) > $array_max)
            {
                $value      = count($value).'['.implode(', ', $result).']';
            }
            else
            {
                $value      = '['.implode(', ', $result).']';
            }

            $is_quoted      = false;
        }
        else
        {
            $value          = $this->get_value_type($value);
            $is_quoted      = false;
        }

        if($is_quoted)
        {
            $value          = '\''.$value.'\'';
        }

        return $value;
    }
}