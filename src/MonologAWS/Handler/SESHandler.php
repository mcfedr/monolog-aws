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
use Monolog\Handler\MailHandler;

class SESHandler extends MailHandler {
	
	protected $to;
	protected $from;
	protected $subject;
	protected $ses;
	
	protected $logger;
	
	/**
	* Set the basics of emails - $to, $subject, $from 
	* Note that $from must be a verified email or domain
	* This can be done on the aws control panel or AmazonSES->verify_email_address
	* Also you must have SES in production, or $to must also be verified.
	*
	* For the $keys pass whatever is needed to create Amazon objects in your app
	* This might an array with key and secret values
	* or null/name of profile if you are using config.inc.php
	* More info http://aws.amazon.com/sdkforphp/
	*
    * @param string|array $to      The receiver of the mail
    * @param string       $subject The subject of the mail
    * @param string       $from    The sender of the mail
	* @param string|array $keys    AWS credentials
    * @param integer      $level   The minimum logging level at which this handler will be triggered
    * @param boolean      $bubble  Whether the messages that are handled can bubble up the stack or not
	*/
	public function __construct($to, $subject, $from, $keys = null, $level = Logger::DEBUG, $bubble = true) {
		parent::__construct($level, $bubble);
		$this->to = $to;
		$this->from = $from;
		$this->subject = $subject;
		$this->ses = new \AmazonSES($keys);
	}
	
	/**
	* The region to set SES to.
	* Available options are AmazonSNS::REGION_US_E1
	*/
	public function setRegion($region) {
		$this->ses->set_region($region);
	}
	
	/**
	* @param Monolog\Logger $logger A logger to send errors to when failing to send to SES
	*/
	public function setLogger($logger) {
		$this->logger = $logger;
	}
	
    /**
     * {@inheritDoc}
     */
	protected function send($content, array $records) {
		$r = $this->ses->send_email(
			$this->from,
			array('ToAddresses' => array(
				$this->to
			)),
			array(
				'Subject' => array(
					'Data' => $this->subject,
					'Charset' => 'UTF-8'
				),
				'Body' => array(
					'Text' => array(
						'Data' => $content,
						'Charset' => 'UTF-8'
					)
				)
			)
		);
		if(!$r->isOK() && $this->logger) {
			$this->logger->addError("Failed to send message via AmazonSES", array('response' => $r));
		}
	}
}
