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

use Aws\Ses\Exception\SesException;
use Aws\Ses\SesClient;
use Monolog\Logger;
use Monolog\Handler\MailHandler;
use Psr\Log\LoggerInterface;

class SesHandler extends MailHandler
{
    /**
     * @var array
     */
    protected $to;

    /**
     * @var string
     */
    protected $from;

    /**
     * @var string
     */
    protected $subject;

    /**
     * @var SesClient
     */
    protected $ses;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Set the basics of emails - $to, $subject, $from
     * Note that $from must be a verified email or domain
     * This can be done on the aws control panel or AmazonSES->verify_email_address
     * Also you must have SES in production, or $to must also be verified.
     *
     * For the $sesClient pass a a ready SesClient, depending ony your setup
     * More info http://docs.aws.amazon.com/aws-sdk-php/guide/latest/service-ses.html
     *
     * @param string|array $to The receiver of the mail
     * @param string $subject The subject of the mail
     * @param string $from The sender of the mail
     * @param SesClient $sesClient The Ses client
     * @param integer $level The minimum logging level at which this handler will be triggered
     * @param boolean $bubble Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct($to, $subject, $from, SesClient $sesClient, $level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->to = is_array($to) ? $to : [$to];
        $this->from = $from;
        $this->subject = $subject;
        $this->ses = $sesClient;
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
    protected function send($content, array $records)
    {
        try {
            $this->ses->sendEmail(
                [
                    'Source' => $this->from,
                    'Destination' => [
                        'ToAddresses' => $this->to
                    ],
                    'Message' => [
                        'Subject' => [
                            'Data' => $this->subject,
                            'Charset' => 'UTF-8'
                        ],
                        'Body' => [
                            'Text' => [
                                'Data' => $content,
                                'Charset' => 'UTF-8'
                            ]
                        ]
                    ]
                ]
            );
        } catch (SesException $e) {
            if ($this->logger) {
                $this->logger->error("Failed to send message via AmazonSES", ['exception' => $e]);
            }
        }
    }
}
