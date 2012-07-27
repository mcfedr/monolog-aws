<?php

/*
 * This file is part of the MonologAWS package.
 *
 * Copyright Fred Cox 2012
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace MonologAWS\Handler;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Formatter\JsonFormatter;

class SNSHandler extends AbstractProcessingHandler {
	
	protected $topic_arn;
	protected $subject;
	protected $sns;
	
	protected $logger;
	
	/**
	* $topic_arn is name of a topic that you have created either in the control panel or
	* using AmazonSNS->create_topic
	*
	* For the keys pass whatever is needed to create Amazon objects in your app
	* This might an array with key and secret values
	* or null/name of profile if you are using config.inc.php
	* More info http://aws.amazon.com/sdkforphp/
	*
    * @param string       $topic   The name of the SNS topic to publish to
	* @param string       $subject Used for email subscriptions to the topic
	* @param string|array $keys    AWS credentials
    * @param integer      $level   The minimum logging level at which this handler will be triggered
    * @param boolean      $bubble  Whether the messages that are handled can bubble up the stack or not
	*/
	public function __construct($topic_arn, $subject = null, $keys = null, $level = Logger::DEBUG, $bubble = true) {
		parent::__construct($level, $bubble);
		$this->topic_arn = $topic_arn;
		$this->subject = $subject;
		$this->sns = new \AmazonSNS($keys);
	}
	
	/**
	* The region to set SNS to.
	* Available options are AmazonSNS::REGION_US_E1, AmazonSNS::REGION_US_W1, AmazonSNS::REGION_US_W2, AmazonSNS::REGION_EU_W1, AmazonSNS::REGION_APAC_SE1, AmazonSNS::REGION_APAC_NE1, AmazonSNS::REGION_SA_E1.
	* @param string $region The region SNS should use.
	*/
	public function setRegion($region) {
		$this->sns->set_region($region);
	}
	
	/**
	* @param Monolog\Logger $logger A logger to send errors to when failing to send to SNS
	*/
	public function setLogger($logger) {
		$this->logger = $logger;
	}
	
    /**
     * {@inheritDoc}
     */
	protected function write(array $record) {
		$r = $this->sns->publish(
			$this->topic_arn,
			substr($this->getFormatter()->format($record), 0, 8192),
			array(
				'Subject' => $this->subject
			)
		);
		if(!$r->isOK() && $this->logger) {
			$this->logger->addError("Failed to send message via AmazonSNS", array('response' => $r));
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
