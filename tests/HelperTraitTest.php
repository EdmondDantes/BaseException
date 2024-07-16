<?php declare(strict_types=1);

namespace IfCastle\Exceptions;

class TestedClass {
    use \IfCastle\Exceptions\HelperTrait {
        toString as public _toString;
        getSourceFor as public _getSourceFor;
        getValueType as public _getValueType;
    }
}

class HelperTraitTest               extends \PHPUnit\Framework\TestCase
{

    public function testGetSourceFor()
    {
        $testedObject           = new TestedClass();
        
        $result                 = $testedObject->_getSourceFor(new \Exception('this'));

        $this->assertEquals
        (
            [
                'source'        => __CLASS__,
                'type'          => '->',
                'function'      => 'testGetSourceFor'
            ],
            $result
        );

        $result                 = $testedObject->_getSourceFor(new \Exception('this'), true);

        $this->assertEquals(__CLASS__.'->testGetSourceFor', $result);
    }

    public function dataProviderGetValueType()
    {
        $data_set               = [];

        $data_set[]             = [true, 'TRUE'];
        $data_set[]             = [false, 'FALSE'];
        $data_set[]             = [new \ArrayObject([]), \ArrayObject::class];
        $data_set[]             = [null, 'NULL'];
        $data_set[]             = ['string', 'STRING'];
        $data_set[]             = [1000, 'INTEGER'];
        $data_set[]             = [1.999, 'DOUBLE'];
        $data_set[]             = [[1, 2, 3], 'ARRAY(3)'];
        $data_set[]             = [[], 'ARRAY(0)'];

        $fh                     = fopen('php://memory', 'rw');

        $data_set[]             = [$fh, 'RESOURCE: stream (MEMORY, PHP, w+b) php://memory'];

        return $data_set;
    }

    /**
     * @dataProvider dataProviderGetValueType
     *
     * @param   mixed     $value
     * @param   string    $expected
     */
    public function testGetValueType($value, $expected)
    {
        $testedObject           = new TestedClass();
        
        $result                 = $testedObject->_getValueType($value);

        $this->assertEquals($expected, $result);
    }

    public function dataProviderToString()
    {
        $data_set               = [];

        // $value, $is_quoted, $expected

        $data_set[]             = [true, true, 'TRUE'];
        $data_set[]             = [true, false, 'TRUE'];
        $data_set[]             = [false, true, 'FALSE'];
        $data_set[]             = [false, false, 'FALSE'];
        $data_set[]             = [new \ArrayObject([]), true, \ArrayObject::class];
        $data_set[]             = [new \ArrayObject([]), false, \ArrayObject::class];
        $data_set[]             = [null, true, 'NULL'];
        $data_set[]             = [null, false, 'NULL'];
        $data_set[]             = ['string', true, "'string'"];
        $data_set[]             = ['string', false, 'string'];
        $data_set[]             = [1000, true, "'1000'"];
        $data_set[]             = [1000, false, "1000"];
        $data_set[]             = [1.999, true, "'1.999'"];
        $data_set[]             = [1.999, false, "1.999"];
        $data_set[]             = [[1, 2, 3], true, "[0:'1', 1:'2', 2:'3']"];
        $data_set[]             = [[1, 2, 3], false, "[0:1, 1:2, 2:3]"];
        $data_set[]             = [['key' => 'value'], true, "[key:'value']"];
        $data_set[]             = [[], true, "[]"];
        $data_set[]             = [array_fill(0, 100, 8), false, "100[0:8, 1:8, 2:8, 3:8, 4:8]"];
        $data_set[]             = [[1, []], true, "[0:'1', 1:ARRAY(0)]"];

        $fh                     = fopen('php://memory', 'rw');

        $data_set[]             = [$fh, true, "RESOURCE: stream (MEMORY, PHP, w+b) php://memory"];
        $data_set[]             = [$fh, false, "RESOURCE: stream (MEMORY, PHP, w+b) php://memory"];

        $string                 = array_fill(0, 256, 'A');
        $expected               = array_fill(0, 255, 'A');

        $data_set[]             = [implode($string), true, "'".implode($expected)."…'"];

        return $data_set;
    }

    /**
     * @dataProvider dataProviderToString
     *
     * @param   mixed       $value
     * @param   bool        $is_quoted
     * @param   string      $expected
     */
    public function testToString($value, $is_quoted, $expected)
    {
        $testedObject           = new TestedClass();
        
        $result                 = $testedObject->_toString($value, $is_quoted);

        $this->assertEquals($expected, $result);
    }
}
