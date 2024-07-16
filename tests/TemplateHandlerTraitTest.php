<?php

declare(strict_types=1);

namespace IfCastle\Exceptions;

use PHPUnit\Framework\Attributes\DataProvider;

class TestedTemplateHandler {
    
    use TemplateHandlerTrait {
        handleTemplate as public _handleTemplate;
    }
    
    /**
     * @param string $value
     * @param bool   $isQuoted
     * @param int    $arrayMax
     *
     * @return      string
     */
    protected function toString(mixed $value, bool $isQuoted = false, int $arrayMax = 5): string
    {
        if($isQuoted)
        {
            return '\''.$value.'\'';
        }
        else
        {
            return (string)$value;
        }
    }
}

class TemplateHandlerTraitTest          extends \PHPUnit\Framework\TestCase
{
    
    /**
     * @param string       $template
     * @param   array      $data
     * @param string|array $message
     * @param int|string   $code
     * @param ?\Throwable  $previous
     * @param mixed        $expected
     */
    #[DataProvider('dataProvider')]
    public function test(mixed $template, array $data, string|array|\ArrayObject $message, int|string $code, \Throwable $previous = null, mixed $expected = null)
    {
        $testedObject           = new \IfCastle\Exceptions\TestedTemplateHandler();
        
        if($expected instanceof \Throwable)
        {
            $e              = null;
            
            try
            {
                $testedObject->_handleTemplate($template, $data, $message, $code, $previous);
            }
            catch(\Throwable $e)
            {
            }
            
            $this->assertInstanceOf(get_class($expected), $e);
        }
        else
        {
            $result        = $testedObject->_handleTemplate($template, $data, $message, $code, $previous);
            $this->assertEquals($expected, $result);
        }
    }

    /**
     * @dataSet On
     */
    protected static function data_set_1(): array
    {
        return
        [
            'template'      => 'This test template message with {code} and {previous}',
            'data'          => [],
            'message'       => 'this is test additional message',
            'code'          => 5,
            'previous'      => new \Exception('this new exception'),
            'expected'      => 'This test template message with 5 and this new exception.'
                              .' this is test additional message'
        ];
    }

    /**
     * @dataSet On
     */
    protected static function data_set_2(): array
    {
        return
        [
            'template'      => 'This test template message with {code} and {previous}',
            'data'          => ['previous' => new \Exception('this new exception')],
            'message'       => 'this is test additional message',
            'code'          => 5,
            'previous'      => null,
            'expected'      => 'This test template message with 5 and this new exception.'
                              .' this is test additional message'
        ];
    }

    /**
     * @dataSet On
     */
    protected static function data_set_3(): array
    {
        return
        [
            'template'      => 'This test template message with {code} and {previous}',
            'data'          => ['previous' => new \Exception('this some exception')],
            'message'       => 'this is test additional message',
            'code'          => 5,
            'previous'      => new \Exception('this new exception'),
            'expected'      => 'This test template message with 5 and this new exception.'
                              .' this is test additional message'
        ];
    }

    /**
     * @dataSet On
     */
    protected static function data_set_4(): array
    {
        return
        [
            'template'      => 'This test template message with {code} and {value}',
            'data'          => ['message' => 'this is test additional message', 'value' => 'test-value'],
            'message'       => '',
            'code'          => 5,
            'previous'      => null,
            'expected'      => 'This test template message with 5 and \'test-value\'.'
                              .' this is test additional message'
        ];
    }

    /**
     * @dataSet On
     */
    protected static function data_set_error_1(): array
    {
        return
        [
            'template'      => 'This test template message with {value}',
            'data'          => ['value' => 'test-value'],
            'message'       => new \ArrayObject([]),
            'code'          => 5,
            'previous'      => null,
            'expected'      => new \TypeError()
        ];
    }

    /**
     * @dataSet On
     */
    protected static function data_set_error_2(): array
    {
        return
        [
            'template'      => 'This test template message with {value}',
            'data'          => ['value' => 'test-value'],
            'message'       => '',
            'code'          => '5',
            'previous'      => null,
            'expected'      => new \TypeError()
        ];
    }

    /**
     * @dataSet On
     */
    protected static function data_set_error_3(): array
    {
        return
        [
            'template'      => 765,
            'data'          => ['value' => 'test-value'],
            'message'       => '',
            'code'          => 10,
            'previous'      => null,
            'expected'      => new \TypeError()
        ];
    }

    public static function dataProvider(): array
    {
        $reflection             = new \ReflectionClass(self::class);

        $results                = [];

        foreach($reflection->getMethods(\ReflectionMethod::IS_PROTECTED) as $method)
        {
            if(!str_starts_with($method->getName(), 'data_set_')
               || preg_match('/@dataSet\sOff/im', $method->getDocComment()))
            {
                continue;
            }

            $results[]          = self::{$method->getName()}();
        }

        return $results;
    }
}
