<?php
/**
 * Created by mcfedr on 07/10/14 20:44
 */

namespace Mcfedr\Monolog\Handler;

use Aws\Command;
use Aws\Sns\Exception\SnsException;
use Monolog\Handler\TestHandler;
use Monolog\Logger;

class SnsHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * This tests that no exceptions come out
     */
    public function testSnsHandler()
    {
        $snsMock = $this->getMock('Aws\Sns\SnsClient', ['publish'], [], '', false);

        $snsMock->expects($this->once())
            ->method('publish');

        $logger = new Logger('test', [
            new SnsHandler("arn::test", "test", $snsMock)
        ]);
        $logger->error('The error');
    }

    /**
     * This tests that the handler pushes out exceptions
     */
    public function testSnsWithLoggerHandler()
    {
        $snsMock = $this->getMock('Aws\Sns\SnsClient', ['publish'], [], '', false);

        $snsMock->expects($this->once())
            ->method('publish')
            ->will($this->throwException(new SnsException('Error', new Command('Command'))));

        $testHandler = new TestHandler();
        $handlerLogger = new Logger('handler-logger', [$testHandler]);

        $handler = new SnsHandler("arn::test", "test", $snsMock);
        $handler->setLogger($handlerLogger);
        $logger = new Logger('test', [$handler]);
        $logger->error('The error');

        $this->assertTrue($testHandler->hasErrorRecords());
    }
}
