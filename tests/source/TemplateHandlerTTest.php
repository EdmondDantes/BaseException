<?PHP
namespace Exceptions;

class TemplateHandlerTTest extends \PHPUnit_Framework_TestCase
{
    use TemplateHandlerT;

    protected function to_string($value, $is_quoted = true)
    {
        if($is_quoted)
        {
            return '\''.$value.'\'';
        }
        else
        {
            return (string)$value;
        }
    }

    /**
     * @dataSet On
     */
    protected function data_set_1()
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
    protected function data_set_2()
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
    protected function data_set_3()
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
    protected function data_set_4()
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
    protected function data_set_error_1()
    {
        return
        [
            'template'      => 'This test template message with {value}',
            'data'          => ['value' => 'test-value'],
            'message'       => new \ArrayObject([]),
            'code'          => 5,
            'previous'      => null,
            'expected'      => new \UnexpectedValueException()
        ];
    }

    /**
     * @dataSet On
     */
    protected function data_set_error_2()
    {
        return
        [
            'template'      => 'This test template message with {value}',
            'data'          => ['value' => 'test-value'],
            'message'       => '',
            'code'          => '5',
            'previous'      => null,
            'expected'      => new \UnexpectedValueException()
        ];
    }

    /**
     * @dataSet On
     */
    protected function data_set_error_3()
    {
        return
        [
            'template'      => 765,
            'data'          => ['value' => 'test-value'],
            'message'       => '',
            'code'          => 10,
            'previous'      => null,
            'expected'      => new \UnexpectedValueException()
        ];
    }

    public function data_provider()
    {
        $reflection             = new \ReflectionClass($this);

        $results                = [];

        foreach($reflection->getMethods(\ReflectionMethod::IS_PROTECTED) as $method)
        {
            if(strpos($method->getName(), 'data_set_') !== 0
            || preg_match('/@dataSet\sOff/im', $method->getDocComment()))
            {
                continue;
            }

            $results[]          = $this->{$method->getName()}();
        }

        return $results;
    }

    /**
     * @dataProvider data_provider
     *
     * @param   string          $template
     * @param   array           $data
     * @param   string          $message
     * @param   string          $code
     * @param   \Exception      $previous
     * @param   string          $excepted
     */
    public function test($template, array $data, $message, $code, $previous, $excepted)
    {
        if($excepted instanceof \Exception)
        {
            $e              = null;

            try
            {
                $this->handle_template($template, $data, $message, $code, $previous);
            }
            catch(\Exception $e)
            {
            }

            $this->assertInstanceOf(get_class($excepted), $e);
        }
        else
        {
            $result        = $this->handle_template($template, $data, $message, $code, $previous);
            $this->assertEquals($excepted, $result);
        }
    }
}
