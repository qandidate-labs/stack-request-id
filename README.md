stack-request-id
=====
Middleware for adding a request id to your Symfony Requests

[![Build Status](https://travis-ci.org/qandidate-labs/stack-request-id.svg?branch=master)](https://travis-ci.org/qandidate-labs/stack-request-id)

## Installation
First, add this project to your project's composer.json

```
$ composer require qandidate/stack-request-id ~0.1.0
```

## Setting up
Update your `app.php` to include the middleware:

Before:
```php5
use Symfony\Component\HttpFoundation\Request;

$kernel = new AppKernel($env, $debug);
$kernel->loadClassCache();

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
```

After:
```php5
use Qandidate\Stack\RequestId;
use Qandidate\Stack\UuidRequestIdGenerator;
use Symfony\Component\HttpFoundation\Request;

$kernel = new AppKernel($env, $debug);

// Stack it!
$generator = new UuidRequestIdGenerator(1337);
$stack = new RequestId($kernel, $generator);

$kernel->loadClassCache();

$request = Request::createFromGlobals();
$response = $stack->handle($request);
$response->send();
$kernel->terminate($request, $response);
```

## Adding the request id to your monolog logs
If you use Symfony's [MonologBundle] you can add the request id to your monolog logs by adding the following service definition to your services.xml file:

```XML
<service id="qandidate.stack.request_id.monolog_processor" class="Qandidate\Stack\RequestId\MonologProcessor">
  <tag name="kernel.event_listener" event="kernel.request" method="onKernelRequest" priority="255" />
  <tag name="monolog.processor" />
</service>
```

[MonologBundle]: https://github.com/symfony/MonologBundle

## Adding the request id to responses
If you need to send the request id back with the response you can enable the response header:

```php5
$generator = new UuidRequestIdGenerator(1337);
$stack = new RequestId($kernel, $generator);
$stack->enableResponseHeader();
```

It is also possible to change response header's name:

```php5
$stack->enableResponseHeader('My-Custom-Request-Id');
```

If you don't have access to the `RequestId` object instance (StackPHP, for example) the response header can be set via
the fourth argument of the `RequestId` constructor method.

```php5
$generator = new UuidRequestIdGenerator(1337);
$stack = new RequestId($kernel, $generator, 'X-Request-Id', 'My-Custom-Request-Id');
```

The third argument, for reference, is the name of the header:
- That will be checked for a value before falling back to generating a new request ID,
- Used to store the resulting request ID inside Symfony's request object.

## StackPHP's Middleware Builder
If you are already using [StackPHP](http://stackphp.com), just push the `RequestId` class into the builder.

```php5
$kernel = new AppKernel('dev', true);

$generator = new UuidRequestIdGenerator(1337);
$stack = (new Stack\Builder)
    ->push('Qandidate\Stack\RequestId', $generator, 'X-Request-Id', 'X-Request-Id')
    ->resolve($kernel);

$kernel->loadClassCache();

$request = Request::createFromGlobals();
$response = $stack->handle($request);
$response->send();
$kernel->terminate($request, $response);
```
