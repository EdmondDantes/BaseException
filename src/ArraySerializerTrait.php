<?php declare(strict_types=1);

namespace IfCastle\Exceptions;

trait ArraySerializerTrait
{
    /**
     * The method defines the source of the exception.
     *
     *
     */
    abstract protected function getSourceFor(\Throwable $e, bool $isString = false): array|string;

    /**
     * The method serialized errors BaseExceptionI to an array
     *
     * @param 			array|BaseExceptionInterface $errors array of errors
     */
    protected function errorsToArray(mixed $errors): array
    {
        if($errors instanceof BaseExceptionInterface)
        {
            $errors             = [$errors];
        }

        $results                = [];

        foreach($errors as $error)
        {
            if($error instanceof BaseExceptionInterface)
            {
                /* @var BaseExceptionInterface $error */
                $results[]      = $error->toArray();
            }
            elseif($error instanceof \Throwable)
            {
                /* @var \Exception $error */
                $results[]      =
                [
                    'type'      => $error::class,
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