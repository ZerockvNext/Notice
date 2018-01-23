# Noice

This library is a concise web application notification center to **Laravel 5**.
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

4) Run the command below to publish the package config file `config/entrust.php`:

```shell
php artisan vendor:publish --provider="ZerockvNext\Notice\NoticeServiceProvider"
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

### Send

```php
    Notice::PostOffice()
        ->type(Notice::Consts()->typeMessage())
        ->sender(1)
        ->title('test message')
        ->message('message contents. this is a test message!')
        ->addReceiver(2)
        ->addReceivers([3, 4, 5, 6, 7])
        ->send();
```

In this code, title() and message() are **not necessary**.

```php
Notice::Consts()->typeMessage()
Notice::Consts()->typeNotice()
Notice::Consts()->typeSystem()
```

type() can receive 'message', 'notice' and 'system', default type is 'message'.
