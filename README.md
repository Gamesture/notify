# Notify
Notify is a simple library to send mobile apps notifications to users from PHP.
Its primary usage is to send notifications to lots of users at once so main focus is performance.
For now only supports Apples APNs (Android support probably in distant future).

## Installation
Install with [Composer](http://getcomposer.org)
`composer require gamesture/notify`

## Usage
```php
$provider = new \Notify\Providers\Apple('/path/to/certificate.pem');
$sender = new \Notify\Sender($provider);
$tokens = ['XXX', 'YYY'];
$message = new \Notify\Message('Notification text');
//optionally set custom data:
//$message->badge = 2;
//$message->sound = 'bell';
//$message->custom_fields = ['notification_id' => 7];
$sender->batchSend($message, $tokens);
```
