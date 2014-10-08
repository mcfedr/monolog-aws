# Monolog AWS Handlers

These handlers make it easy to send logs via amazon services

[![Latest Stable Version](https://poser.pugx.org/mcfedr/monolog-aws/v/stable.png)](https://packagist.org/packages/mcfedr/monolog-aws)
[![License](https://poser.pugx.org/mcfedr/monolog-aws/license.png)](https://packagist.org/packages/mcfedr/monolog-aws)
[![Build Status](https://travis-ci.org/mcfedr/monolog-aws.svg?branch=master)](https://travis-ci.org/mcfedr/monolog-aws)

## Usage

### Amazon SES

A mail handler that sends its messages via SES.
It replicates the interface of the other mail handlers. I recommend using it with a `BufferHandler`.

### Amazon SNS

A logger handler mosts messages using the SNS - publish/subscribe service.
Simply specify the topic ans and have your log sent to you via email, sms or however you need.
Note that messages are trimmed to 8KB, in case you are trying to log really long stack traces or something.
If you use a region other than US_EAST you will need to do setRegion on the Handler.

## Tests

To run the tests

    ./vendor/.bin/phpunit
