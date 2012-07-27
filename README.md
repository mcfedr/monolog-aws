# Monolog Amazon Handlers

These handlers make it easy to send logs via amazon services

##Usage

You should probably include the files like you do Monolog, using a classloader.


The two test cases show very basic usage. Do `git submodule init --update` to 
get everything need to run them. You must also put in your aws keys.

You can give another Logger object to either of the handlers and they will log errors sending
messages via amazon. This can be helpful to test your setup.

## Amazon SES

A mail handler that sends its messages via SES.
It replicates the interface of the other mail handlers. I recommend using it with a `BufferHandler`.

## Amazon SNS

A logger handler mosts messages using the SNS - publish/subscribe service.
Simply specify the topic ans and have your log sent to you via email, sms or however you need.
Note that messages are trimmed to 8KB, incase you are trying to log really long stacktraces or something.
If you use a region other than US_EAST you will need to do setRegion on the Handler.
