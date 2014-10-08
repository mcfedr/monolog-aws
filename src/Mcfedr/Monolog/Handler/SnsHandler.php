<?php

/*
 * This file is part of the AWS Monolog package.
 *
 * Copyright Fred Cox 2012
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Mcfedr\Monolog\Handler;

use Aws\Sns\Exception\SnsException;
use Aws\Sns\SnsClient;
use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Formatter\JsonFormatter;
use Psr\Log\LoggerInterface;

class SnsHandler extends AbstractProcessingHandler
{

    /**
     * @var string
     */
    protected $topicArn;

    /**
     * @var null|string
     */
    protected $subject;

    /**
     * @var SnsClient
     */
    protected $sns;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * $topic_arn is name of a topic that you have created either in the control panel or
     * using AmazonSNS->create_topic
     *
     * For the $snsClient pass a a ready SnsClient, depending ony your setup
     * More info http://docs.aws.amazon.com/aws-sdk-php/guide/latest/service-sns.html
     *
     * @param string $topicArn The name of the SNS topic to publish to
     * @param string $subject Used for email subscriptions to the topic
     * @param SnsClient $snsClient The Sns client
     * @param integer $level The minimum logging level at which this handler will be triggered
     * @param boolean $bubble Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct($topicArn, $subject, SnsClient $snsClient, $level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->topicArn = $topicArn;
        $this->subject = $subject;
        $this->sns = $snsClient;
    }

    /**
     * @param LoggerInterface $logger A logger to send errors to when failing to send to SES
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    protected function write(array $record)
    {
        try {
            $this->sns->publish(
                [
                    'TopicArn' => $this->topicArn,
                    'Message' => mb_strcut($this->getFormatter()->format($record), 0, 262144),
                    'Subject' => $this->subject
                ]
            );
        } catch (SnsException $e) {
            if ($this->logger) {
                $this->logger->error("Failed to send message via AmazonSNS", ['exception' => $e]);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultFormatter()
    {
        return new JsonFormatter();
    }

}
