<?php declare(strict_types=1);

namespace Exceptions;

trait ArraySerializerT
{
    /**
     * The method defines the source of the exception.
     *
     * @param       \Throwable $e
     * @param       boolean    $isString
     *
     * @return      array|string
     */
    abstract protected function getSourceFor(\Throwable $e, bool $isString = false): array|string;

    /**
     * The method serialized errors BaseExceptionI to an array
     *
     * @param 			array|BaseExceptionI    $errors	    array of errors
     *
     * @return          array
     */
    protected function errorsToArray(mixed $errors): array
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
                $results[]      = $error->toArray();
            }
            elseif($error instanceof \Throwable)
            {
                /* @var \Exception $error */
                $results[]      =
                [
                    'type'      => get_class($error),
                    'source'    => $this->getSourceFor($error),
                    'message'   => $error->getMessage(),
                    'code'      => $error->getCode()
                ];
            }
        }

        return $results;
    }

    /**
     * The method deserialized array of array to array of errors.
     *
     * @param 			array 						$array      array of array
     * @param           string                      $class      class for exception
     *
     * @return          BaseException[]
     *
     * @throws          \UnexpectedValueException
     */
    protected function arrayToErrors(array $array, string $class = BaseException::class): array
    {
        $results                = [];

        foreach($array as $error)
        {
            if(!is_array($error))
            {
                throw new \UnexpectedValueException('$error must be array');
            }

            $results[]          = new $class($error);
        }

        return $results;
    }
}