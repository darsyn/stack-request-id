Request ID for Stack
====================

Middleware for adding a request ID to your Symfony requests.

[![Build Status](https://travis-ci.org/darsyn/stack-request-id.svg?branch=master)](https://travis-ci.org/darsyn/stack-request-id)

## Installation

First, add this project to your project's composer.json

```bash
$ composer require darsyn/stack-request-id ^1.0
```

## Setting up
Update your `app.php` to include the middleware:

Before:
```php
use Symfony\Component\HttpFoundation\Request;

$kernel = new AppKernel($env, $debug);
$kernel->loadClassCache();

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
```

After:
```php
use Darsyn\Stack\RequestId\Injector;
use Darsyn\Stack\RequestId\UuidGenerator;
use Symfony\Component\HttpFoundation\Request;

$kernel = new AppKernel($env, $debug);

// Stack it! Node name is optional.
$generator = new UuidGenerator($nodeName);
$stack = new Injector($kernel, $generator);

$kernel->loadClassCache();

$request = Request::createFromGlobals();
$response = $stack->handle($request);
$response->send();
$kernel->terminate($request, $response);
```

## Adding the RequestId to your Monolog logs

If you use Symfony's [MonologBundle] you can add the request ID to your Monolog logs by adding the following service
definition to your services.yml file:

```yaml
services:

    darsyn.stack.request_id.monolog_processor:
        class: Darsyn\Stack\RequestId\Monolog\Processor
        tags:
            - { name: kernel.event_listener, event: kernel.request, method: onKernelRequest, priority: 255 }
            - { name: monolog.processor }
```

## Changing the Response Header

The default is `X-Request-Id`.

```php
$stack = new Injector($kernel, $generator, 'Request-Id');
```

## Disabling the Response Header

```php
$stack = new Injector($kernel, $generator, null, false);
```

[MonologBundle]: https://github.com/symfony/MonologBundle
