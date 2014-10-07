# Monolog AWS Handlers

These handlers make it easy to send logs via amazon services

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

To run the tests you must setup the `~/.aws/credentials` file as described in
the [documentation](http://docs.aws.amazon.com/aws-sdk-php/guide/latest/credentials.html#credential-profiles)
