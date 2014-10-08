<?php
/**
 * Created by mcfedr on 07/10/14 20:44
 */

namespace Mcfedr\Monolog\Handler;

use Aws\Ses\SesClient;
use Monolog\Handler\TestHandler;
use Monolog\Logger;

class SesHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * This tests that no exceptions come out
     */
    public function testSesHandler()
    {
        $logger = new Logger('test', [
            new SesHandler("test@example.com", "test", "test@example.com", SesClient::factory([
                'region' => 'eu-west-1'
            ]))
        ]);
        $logger->error('The error');
    }

    /**
     * This tests that the handler pushes out exceptions
     */
    public function testSesWithLoggerHandler()
    {
        $testHandler = new TestHandler();
        $handlerLogger = new Logger('handler-logger', [$testHandler]);

        $handler = new SesHandler("test@example.com", "test", "test@example.com", SesClient::factory([
            'region' => 'eu-west-1'
        ]));
        $handler->setLogger($handlerLogger);
        $logger = new Logger('test', [$handler]);
        $logger->error('The error');

        $this->assertTrue($testHandler->hasErrorRecords());
    }
}
