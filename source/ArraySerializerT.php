<?PHP
namespace Exceptions;

trait ArraySerializerT
{
    /**
     * The method defines the source of the exception.
     *
     * @param       \Exception      $e
     * @param       boolean         $is_string
     *
     * @return      array|string
     */
    abstract protected function get_source_for(\Exception $e, $is_string = false);

    /**
     * The method serialized errors BaseExceptionI to an array
     *
     * @param 			array|BaseExceptionI    $errors	    array of errors
     *
     * @return          array
     */
    protected function errors_to_array($errors)
    {
        if($errors instanceof BaseExceptionI)
        {
            $errors             = [$errors];
        }

        $results                = [];

        foreach($errors as $error)
        {
            if($error instanceof BaseExceptionI)
            {
                /* @var BaseExceptionI $error */
                $results[]      = $error->to_array();
            }
            elseif($error instanceof \Exception)
            {
                /* @var \Exception $error */
                $results[]      =
                [
                    'type'      => get_class($error),
                    'source'    => $this->get_source_for($error),
                    'message'   => $error->getMessage(),
                    'code'      => $error->getCode(),
                    'data'      => $error->getTrace()
                ];
            }
        }
        return $results;
    }

    /**
     * The method deserialized array of array to array of errors.
     *
     * @param 			array 						$array array of array
     *
     * @return          BaseException[]
     *
     * @throws          \UnexpectedValueException
     */
    protected function array_to_errors(array $array)
    {
        $results                = [];

        foreach($array as $error)
        {
            if(!is_array($error))
            {
                throw new \UnexpectedValueException('$error must be array');
            }

            $results[]          = new BaseException($error);
        }

        return $results;
    }
}