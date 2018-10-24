# codeigniter-symfony2-bridge

[![Build Status](https://travis-ci.org/piotrpolak/codeigniter-symfony2-bridge.svg)](https://travis-ci.org/piotrpolak/codeigniter-symfony2-bridge)
[![Maintainability](https://api.codeclimate.com/v1/badges/356328690ebe2cc991d1/maintainability)](https://codeclimate.com/github/piotrpolak/codeigniter-symfony2-bridge/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/356328690ebe2cc991d1/test_coverage)](https://codeclimate.com/github/piotrpolak/codeigniter-symfony2-bridge/test_coverage)

CodeIgniter library that facilitates integration of Symfony services into a CodeIgniter application in a compact yet
elegant way.

As version 1.0.0 the code has been decoupled from the CodeIgniter framework. This means that is can pottentially be used
to integrate any 3rd party framework with Symfony kernel/container.

If you want to use the initial [CodeIgniter
library, please check the 0.1.0 release](https://github.com/piotrpolak/codeigniter-symfony2-bridge/releases/tag/0.1.0).

## Some history and most common scenario

The library was written for a LTS project that had to be integrated with a brand new Symfony2+ application.
The business logic is encapsulated within services managed by Symfony2+ dependency injection component and consumed both
by the Symfony2+ bundles and CodeIgniter components.

Where possible, the service methods are taking variables of primitive types as input for convenience - an approach
that can be compared to designing a friendly SOAP interface.

The output of the methods can be both of primitive types and complex objects as the bridge and the Symfony2+ kernel
itself manages the PSR class autoload. The bridge does not create any overhead related to communication and parameter
serialization.

I am a big fan of Symfony2+ and good software architecture with a nostalgia for CodeIgniter.
I started working with CodeIgniter back in 2006 with version 1.5.0, I know very well its architecture and I am 
aware of all its architectural constraints - I no longer use it for new projects but I maintain some legacy systems
since 2008 with success.

## Compatibility
CodeIgniter: 2.0 - 3.1+

Symfony: 2.0+, 3.0+, 4.0+

## License
The software is dual-licensed under:
 * [MIT](LICENSE_MIT)
 * [GPL v3](LICENSE_GPL_V3)

## Testing

```bash
composer install --prefer-dist && ./vendor/bin/phpunit -v
```

To test in an arbitrary version combination, please use (`sudo` might be required to connect to the Docker daemon):
```bash
PHP_VERSION=7.2 bin/test_in_docker.sh
```

## Examples

### Getting Symfony2+ service container
```php
$bridge = new \PiotrPolak\CodeIgniterSymfonyBridge\Bridge('./symfonyRootDir');
try {
    // \Symfony\Component\DependencyInjection\Container
    $container = $bridge->getContainer();
} catch(Exception $e) {
    // Unable to initialize Symfony2+ kernel
}
```
More information about Symfony service container and depencency injection:
* http://symfony.com/doc/current/book/service_container.html
* http://symfony.com/doc/current/components/dependency_injection/introduction.html

### Consuming a service
```php
$bridge = new \PiotrPolak\CodeIgniterSymfonyBridge\Bridge('./symfonyRootDir');
try {
    $result = $bridge->getContainer()->get('my_service')->businessLogicServiceMethod('parameter of primitive type'));
} catch(\Exception $e) {
    // Unable to initialize Symfony2+ kernel
}
```

When designing a service, parameters of primitive types are preferred - this creates minimum overhead and makes the
service integration easy. The output of the service method can be of any type.

### Getting Doctrine2 entity manager
```php
$bridge = new \PiotrPolak\CodeIgniterSymfonyBridge\Bridge('./symfonyRootDir');
try {
    $em = $bridge->getContainer()->get('doctrine')->getManager();
} catch(\Exception $e) {
    // Unable to initialize Symfony2+ kernel
}
```

Getting access to the Doctrine2 entity manager makes you able to get access to the entities and related repositories.

### Loading an arbitrary class defined within a Symfony2+ bundle
```php
$bridge = new \PiotrPolak\CodeIgniterSymfonyBridge\Bridge('./symfonyRootDir');
try {
    $container = $bridge->getContainer(); // Initializes Symfony2+ PSR class loader
    $imageHelper = new \Pepis\ImageManipulationBundle\Helpers\ImageHelper();
} catch(Exception $e) {
    // Unable to initialize Symfony2+ kernel
}
```