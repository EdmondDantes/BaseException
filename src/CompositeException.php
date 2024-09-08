<?php
declare(strict_types=1);

namespace IfCastle\Exceptions;

class CompositeException            extends LoggableException
{
    public function __construct(string $message = 'Multiple exceptions occurred', \Throwable ...$exceptions)
    {
        $previous                   = null;
        
        if(!empty($exceptions[0])) {
            $previous               = $exceptions[0];
        }
        
        parent::__construct([
            'template'              => 'Multiple exceptions occurred {context}',
            'context'               => $message,
            'exceptions'            => $exceptions,
            'previous'              => $previous
        ]);
    }
}