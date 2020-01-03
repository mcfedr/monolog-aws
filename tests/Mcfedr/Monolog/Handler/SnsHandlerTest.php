<?php

declare(strict_types=1);
/**
 * Created by mcfedr on 07/10/14 20:44.
 */

namespace Mcfedr\Monolog\Handler;

use Aws\Command;
use Aws\Sns\Exception\SnsException;
use Aws\Sns\SnsClient;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class SnsHandlerTest extends TestCase
{
    /**
     * This tests that no exceptions come out.
     */
    public function testSnsHandler(): void
    {
        $snsMock = $this->getMockBuilder(SnsClient::class)
            ->disableOriginalConstructor()
            ->addMethods(['publish'])
            ->getMock()
        ;

        $snsMock->expects(static::once())
            ->method('publish')
        ;

        $logger = new Logger('test', [
            new SnsHandler('arn::test', 'test', $snsMock),
        ]);
        $logger->error('The error');
    }

    /**
     * This tests that the handler pushes out exceptions.
     */
    public function testSnsWithLoggerHandler(): void
    {
        $snsMock = $this->getMockBuilder(SnsClient::class)
            ->disableOriginalConstructor()
            ->addMethods(['publish'])
            ->getMock()
        ;

        $snsMock->expects(static::once())
            ->method('publish')
            ->will(static::throwException(new SnsException('Error', new Command('Command'))))
        ;

        $testHandler = new TestHandler();
        $handlerLogger = new Logger('handler-logger', [$testHandler]);

        $handler = new SnsHandler('arn::test', 'test', $snsMock);
        $handler->setLogger($handlerLogger);
        $logger = new Logger('test', [$handler]);
        $logger->error('The error');

        static::assertTrue($testHandler->hasErrorRecords());
    }
}
