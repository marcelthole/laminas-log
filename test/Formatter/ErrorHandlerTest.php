<?php

/**
 * @see       https://github.com/laminas/laminas-log for the canonical source repository
 * @copyright https://github.com/laminas/laminas-log/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-log/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Log\Formatter;

use DateTime;
use Laminas\Log\Formatter\ErrorHandler;
use LaminasTest\Log\TestAsset\NotStringObject;
use LaminasTest\Log\TestAsset\StringObject;

/**
 * @group      Laminas_Log
 */
class ErrorHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testFormat()
    {
        $date = new DateTime();

        $event = array(
            'timestamp'    => $date,
            'message'      => 'test',
            'priority'     => 1,
            'priorityName' => 'CRIT',
            'extra' => array (
                'errno' => 1,
                'file'  => 'test.php',
                'line'  => 1,
                'context' => array('object' => new DateTime(), 'string' => 'test')
            )
        );
        $formatter = new ErrorHandler();
        $output = $formatter->format($event);

        $this->assertEquals($date->format('c') . ' CRIT (1) test (errno 1) in test.php on line 1', $output);
    }

    public function testSetDateTimeFormat()
    {
        $formatter = new ErrorHandler();

        $this->assertEquals('c', $formatter->getDateTimeFormat());
        $this->assertSame($formatter, $formatter->setDateTimeFormat('r'));
        $this->assertEquals('r', $formatter->getDateTimeFormat());
    }

    public function testComplexEvent()
    {
        $date = new DateTime();
        $stringObject = new StringObject();
        $event = array(
                'timestamp' => $date,
                'message' => 'test',
                'priority' => 1,
                'priorityName' => 'CRIT',
                'extra' => array(
                        'errno' => 1,
                        'file' => 'test.php',
                        'line' => 1,
                        'context' => array(
                                'object1' => new StringObject(),
                                'object2' => new NotStringObject(),
                                'string' => 'test1',
                                'array' => array(
                                        'key' => 'test2'
                                )
                        )
                )
        );
        $formatString = '%extra[context][object1]% %extra[context][object2]% %extra[context][string]% %extra[context][array]% %extra[context][array][key]%';
        $formatter = new ErrorHandler($formatString);
        $output = $formatter->format($event);
        $this->assertEquals($stringObject->__toString() .' %extra[context][object2]% test1 %extra[context][array]% test2', $output);
    }


}
