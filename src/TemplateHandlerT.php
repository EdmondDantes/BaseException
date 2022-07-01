<?php declare(strict_types=1);
namespace Exceptions;

trait TemplateHandlerT
{
    /**
     * Returns string view for the $value
     *
     * @param   mixed $value
     * @param   bool  $isQuoted
     * @param   int   $arrayMax
     *
     * @return  string
     */
    abstract protected function toString(mixed $value, bool $isQuoted = false, int $arrayMax = 5): string;

    /**
     * Handles the template message
     *
     * @param   string              $template       Template
     * @param   array               $data           Extended data
     * @param   string              $message        Message of exception
     * @param   int                 $code           Code
     * @param   ?\Throwable         $previous       Previous Exception
     *
     * @throws  \UnexpectedValueException
     *
     * @return string
     */
    protected function handleTemplate
    (
        string      $template,
        array       $data,
        string      $message,
        int         $code,
        \Throwable  $previous   = null
    ): string
    {
        // for PSR-3 previous also interpreted as the exception
        if(isset($data['previous']) && $previous === null)
        {
            $previous           = $data['previous'];
            unset($data['previous']);
        }
        elseif(isset($data['previous']) && $previous !== null)
        {
            unset($data['previous']);
        }

        $previous               = is_null($previous) ? '' : $previous->getMessage();

        // Mixed to context message code and previous
        $context                =
        [
            '{code}'            => $code,
            '{previous}'        => $previous,
            // for PSR-3 previous also interpreted as the exception
            '{exception}'       => $previous
        ];

        // normalize additional message
        if(empty($message) && isset($data['message']))
        {
            $message            = $data['message'];
            unset($data['message']);
        }

        foreach($data as $key => $value)
        {
            $context['{'.$key.'}'] = $this->toString($value, true);
        }

        $template               = strtr($template, $context);

        // Message added to the result like extended message
        if(!empty($message))
        {
            $template           .= '. '.$message;
        }

        return $template;
    }
}