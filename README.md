# Noice (Laravel 5 Notice package)

This library is a concise web application notification center to **Laravel 5**

I have only tested on Laravel 5, not sure if it's available on Laravel4.

## Contents
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [License](#license)

## Installation

1. Install use composer
```shell
composer require "zerockvnext/notice:~1.0"
```

2) Open your `config/app.php` and add the following to the `providers` array:

```php
ZerockvNext\Notice\NoticeServiceProvider::class,
```

3) In the same `config/app.php` and add the following to the `aliases ` array:

```php
'Notice' => ZerockvNext\Notice\NoticeFacade::class,
```

4) Run the command below to publish the package config & migration files:

```shell
php artisan vendor:publish --provider="ZerockvNext\Notice\NoticeServiceProvider"
```

5) Run migrate
```shell
php artisan migrate
```

## Configuration

1. Open your `config/notice.php` and add the following to the `orm` array:

```php
'orm' => [
    'user' => \App\User::class
],
```
chang it to your User ORM

## Usage

### Send Message

```php
    Notice::PostOffice()
        ->type(Notice::Consts()->typeMessage())
        ->sender(1) // Sender ID
        ->title('test message')
        ->message('message contents. this is a test message!')
        ->addReceiver(2) // Receiver ID
        ->addReceivers([3, 4, 5, 6, 7]) // Receivers
        ->send();
```

In this code, title() and message() are **not necessary**.

```php
Notice::Consts()->typeMessage() // message, someone send message to someone else.
Notice::Consts()->typeNotice() // notice, announcement by someone.
Notice::Consts()->typeSystem() // system, messages sent by the system.
```

type() can receive 'message', 'notice' and 'system', default type is 'message'.

### MailBox

#### Receive Message

```php
$Messages = Notice::MailBox()
    ->receiver(4) // Receiver ID
    ->getPagedMessages(1, 10); // Page, Limited, Type = null

$Totals = Notice::MailBox()
    ->totalMessages(); // Type = null

return [
    'messages' => $Messages, // Paged messages
    'totals'   => $Totals, // Total count of messages
];
```

or like this

```php
Notice::MailBox()->receiver(4) // Receiver ID
$Messages = Notice::MailBox()->getPagedMessages(1, 10, Notice::Consts()->typeMessage());
$Totals   = Notice::MailBox()->totalMessages(Notice::Consts()->typeMessage());

return [
    'messages' => $Messages, // Paged messages
    'totals'   => $Totals, // Total count of messages
];
```

If you set the value of $Type, then this method will return the result of the corresponding type.

#### Count Messages

```php
Notice::MailBox()->receiver(4);

Notice::MailBox()->hasUnread(); // Type = null, return bool
Notice::MailBox()->hasUnread(Notice::Consts()->typeMessage());

Notice::MailBox()->totalUnread(); // Type = null, return unread count num
Notice::MailBox()->totalUnread(Notice::Consts()->typeMessage());

Notice::MailBox()->totalMessages(); // Type = null, return count num
Notice::MailBox()->totalMessages(Notice::Consts()->typeMessage());

Notice::MailBox()->countUnread(); // Type = null
// return array like this ['message' => 5, 'notice' => 2, 'system' => 3]

Notice::MailBox()->countUnread(Notice::Consts()->typeMessage());
// return array like this ['message' => 5]
```

#### Get Specified Message

```php
$Message = Notice::MailBox()->receiver(4)->getTransfer(1) // Transfer ID

return [
    'message' => $Message, // Message
];
```
Note: Only message for this receiver can be obtained.

#### Read / Unread

```php
Notice::MailBox()->receiver(4)->read(1) // Transfer ID
Notice::MailBox()->receiver(4)->read([1,2,3,4]) // Transfer IDs

Notice::MailBox()->receiver(4)->readAll();
Notice::MailBox()->receiver(4)->readAll(Notice::Consts()->typeMessage()); // Type

Notice::MailBox()->receiver(4)->unread(1) // Transfer ID
Notice::MailBox()->receiver(4)->unread([1,2,3,4]) // Transfer IDs
```
Note: Only the corresponding receiver can modify message status.

#### Remove Message
```php
Notice::MailBox()->receiver(4)->remove(1) // Transfer ID
Notice::MailBox()->receiver(4)->remove([1,2,3,4]) // Transfer IDs
```

#### View Remove Message
```php
Notice::MailBox()->mode(Notice::Consts()->modeRecycled());

$Messages = Notice::MailBox()
    ->receiver(4)
    ->getPagedMessages(1, 10);

$Totals = Notice::MailBox()->totalMessages();

return [
    'messages' => $Messages, // Paged messages
    'totals'   => $Totals, // Total count of messages
];
```

you can use those values:

```php
Notice::Consts()->modeAll() // return null, all messages
Notice::Consts()->modeWithoutRecycled() // return true, default, only not recycled
Notice::Consts()->modeRecycled() // return false, only recycled
```

## License
MIT license.
