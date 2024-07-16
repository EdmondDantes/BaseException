<?php

declare(strict_types=1);

namespace IfCastle\Exceptions;

class TestClass
{
    use \IfCastle\Exceptions\HelperTrait {
        toString as public _toString;
    }
    
    use \IfCastle\Exceptions\ArraySerializerTrait {
        errorsToArray as public _errorsToArray;
        arrayToErrors as public _arrayToErrors;
    }
}

class ArraySerializerTraitTest extends \PHPUnit\Framework\TestCase
{
    public function testErrors_to_array()
    {
        $testedObject                   = new TestClass();
        
        // ones exception
        $exceptions                     = $testedObject->_errorsToArray
        (
            new BaseException
            ([
                'message'               => 'test message1',
                'code'                  => 5,
                'exdata'                => [2,3,4]
            ])
        );

        $this->assertTrue(is_array($exceptions), '$exceptions must be array');
        $this->assertTrue(count($exceptions) === 1, '$exceptions must have 1 elements');

        $res                            = array_shift($exceptions);

        $this->assertArrayHasKey('message', $res);
        $this->assertArrayHasKey('code', $res);
        $this->assertArrayHasKey('data', $res);
        $this->assertArrayHasKey('template', $res);
        $this->assertArrayHasKey('exdata', $res['data']);

        // several exceptions
        $errors                         =
        [
            new BaseException
            ([
                'message'               => 'test message1',
                'code'                  => 5,
                'exdata'                => [2,3,4]
            ]),
            new LoggableException
            ([
                'message'               => 'test message2',
                'code'                  => 6,
                'template'              => 'this LoggableException with {code}',
                'exdata'                => [3,2,1]
            ]),
            new \Exception('test message3', 7),
            new BaseException('test message4',8)
        ];

        $exceptions                     = $testedObject->_errorsToArray($errors);

        $this->assertTrue(is_array($exceptions), '$exceptions must be array');
        $this->assertTrue(count($exceptions) === 4, '$exceptions must have three elements');

        // 1.
        $res                            = array_shift($exceptions);

        $this->assertArrayHasKey('message', $res);
        $this->assertArrayHasKey('code', $res);
        $this->assertArrayHasKey('data', $res);
        $this->assertArrayHasKey('template', $res);

        $this->assertEquals('test message1', $res['message']);
        $this->assertEquals(5, $res['code']);
        $this->assertTrue
        (
            isset($res['data']['exdata']) &&
            is_array($res['data']['exdata']) &&
            implode('|', $res['data']['exdata']) === '2|3|4',
            'exdata failed'
        );

        // 2.
        $res                            = array_shift($exceptions);

        $this->assertArrayHasKey('message', $res);
        $this->assertArrayHasKey('code', $res);
        $this->assertArrayHasKey('data', $res);
        $this->assertArrayHasKey('template', $res);

        $this->assertEquals('test message2', $res['message']);
        $this->assertEquals('this LoggableException with {code}', $res['template']);
        $this->assertEquals(6, $res['code']);
        $this->assertTrue
        (
            isset($res['data']['exdata']) &&
            is_array($res['data']['exdata']) &&
            implode('|', $res['data']['exdata']) === '3|2|1',
            'exdata failed'
        );

        $res                            = array_shift($exceptions);

        $this->assertArrayHasKey('message', $res);
        $this->assertArrayHasKey('code', $res);
        //$this->assertArrayHasKey('data', $res);

        $this->assertEquals('test message3', $res['message']);
        $this->assertEquals(7, $res['code']);

        $res                            = array_shift($exceptions);

        $this->assertArrayHasKey('message', $res);
        $this->assertArrayHasKey('code', $res);
        $this->assertArrayHasKey('data', $res);
        $this->assertArrayHasKey('template', $res);

        $this->assertEquals('test message4', $res['message']);
        $this->assertEquals(8, $res['code']);
    }

    public function testArray_to_errors()
    {
        $testedObject                   = new TestClass();
        
        $array                          = [];

        for($i=0; $i<3; $i++)
        {
            $array[]                    =
            [
                'message'               => 'test message'.($i+5),
                'template'              => 'test template with {code} and {exdata}',
                'code'                  => ($i+10),
                'exdata'                => ($i-10)
            ];
        }

        $results = $testedObject->_arrayToErrors($array);

        $i                              = 0;

        foreach($results as $exception)
        {
            $this->assertInstanceOf(BaseExceptionInterface::class, $exception);
            $this->assertEquals('test template with {code} and {exdata}', $exception->template());
            $this->assertEquals
            (
                'test template with '.($i+10).' and \''.($i-10).'\'. test message'.($i+5),
                $exception->getMessage(),
                '$exception->getMessage() failed'
            );
            $this->assertEquals(($i+10), $exception->getCode(), '$exception->getCode() failed');
            $data = $exception->getExceptionData();
            $this->assertTrue(is_array($data), '$exception.data must be array');
            $this->assertArrayHasKey('exdata', $data, '$exception.data.exdata no exists');
            $this->assertEquals(($i-10), $data['exdata'], '$exception.data.exdata failed');

            $i++;
        }
    }
    
    public function testArray_to_errors_error()
    {
        $testedObject                   = new TestClass();
        
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('$error must be array');
        $testedObject->_arrayToErrors([1,2,3]);
    }
}
