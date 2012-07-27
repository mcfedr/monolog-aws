<?php

/*
 * This file is part of the MonologAWS package.
 *
 * Copyright Fred Cox 2012
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$dir = dirname(__FILE__);
require_once "$dir/../aws-sdk-for-php/sdk.class.php";
require_once "$dir/../ClassLoader/UniversalClassLoader.php";

$loader = new \Symfony\Component\ClassLoader\UniversalClassLoader();
$loader->register();
$loader->registerNamespace('Monolog', "$dir/../monolog/src");
$loader->registerNamespace('MonologAWS', "$dir/../src");

$keys = array('key'=>'###', 'secret'=>'###');

$testLogger = new \Monolog\Logger('sns-logger');
$logger = new \Monolog\Logger('sns-test-logger');
$h = new \MonologAWS\Handler\SNSHandler(
	'arn:aws:sns:eu-west-1: ... ',
	'sns-test-logger',
	$keys
);
$h->setRegion(AmazonSNS::REGION_EU_W1);
$h->setLogger($testLogger);
$logger->pushHandler($h);

$logger->addWarning('Help! everything is going wrong!');


