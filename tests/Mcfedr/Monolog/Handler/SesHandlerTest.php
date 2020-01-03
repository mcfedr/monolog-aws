<?php

declare(strict_types=1);
/**
 * Created by mcfedr on 07/10/14 20:44.
 */

namespace Mcfedr\Monolog\Handler;

use Aws\Command;
use Aws\Ses\Exception\SesException;
use Aws\Ses\SesClient;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class SesHandlerTest extends TestCase
{
    /**
     * This tests that no exceptions come out.
     */
    public function testSesHandler(): void
    {
        $sesMock = $this->getMockBuilder(SesClient::class)
            ->disableOriginalConstructor()
            ->addMethods(['sendEmail'])
            ->getMock()
        ;

        $sesMock->expects(static::once())
            ->method('sendEmail')
        ;

        $logger = new Logger('test', [
            new SesHandler('test@example.com', 'test', 'test@example.com', $sesMock),
        ]);
        $logger->error('The error');
    }

    /**
     * This tests that the handler pushes out exceptions.
     */
    public function testSesWithLoggerHandler(): void
    {
        $sesMock = $this->getMockBuilder(SesClient::class)
            ->disableOriginalConstructor()
            ->addMethods(['sendEmail'])
            ->getMock()
        ;

        $sesMock->expects(static::once())
            ->method('sendEmail')
            ->will(static::throwException(new SesException('Error', new Command('Command'))))
        ;

        $testHandler = new TestHandler();
        $handlerLogger = new Logger('handler-logger', [$testHandler]);
        $handler = new SesHandler('test@example.com', 'test', 'test@example.com', $sesMock);
        $handler->setLogger($handlerLogger);
        $logger = new Logger('test', [$handler]);
        $logger->error('The error');

        static::assertTrue($testHandler->hasErrorRecords());
    }
}
