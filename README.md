# codeigniter-symfony2-bridge
CodeIgniter library that facilitates integration of Symfony2 services into a CodeIgniter application in a compact yet
elegant way.

## Some history and most common scenario description

The library was written for a LTS project that had to be integrated with a brand new Symfony2 application.
The business logic is encapsulated within services managed by Symfony2 dependency injection component and consumed both
by the Symfony2 bundles and CodeIgniter controllers.

Where possible, the service methods is taking variables of primitive types as input for convenience
(approach similar to designing a friendly SOAP interface).

The output of the methods can be both of primitive types and complex objects as the bridge and the kernel itself
manages the PSR autoload. The bridge does not create any overhead related to communication and parameter serialization.

## Naming convention
Method naming convention is intentionally camelCase, the same as in Symfony2

## Compatibility
CodeIgniter: 2.0 - 3.0
Symfony2: 2.0 - 2.7

## License
[GPL v3](http://www.gnu.org/licenses/gpl-3.0.txt)

## Loading the library
You can either load the library manually every time you use
```$this->load->load('Symfony2_bridge', array('root_dir' => 'path/to/symfony2/application/'));```

Or make it globally available by adding it to CodeIgniter autload
[application/config/autoload.php](https://github.com/bcit-ci/CodeIgniter/blob/develop/application/config/autoload.php#L63)
```$autoload['libraries'] = array('Symfony2_bridge');```

Please note that the default `$params['root_dir']` is `../../app/` - this is where your `bootstrap.php.cache` and
`AppKernel.php` lives.

## Examples

### Getting Symfony2 service container
```php
try {
    // \Symfony\Component\DependencyInjection\Container
    $container = $this->symfony2_bridge->getContainer();
} catch(Exception $e) {
    // Unable to initialize Symfony2 kernel
}
```
More information about Symfony2 service container and depencency injection:
* http://symfony.com/doc/current/book/service_container.html
* http://symfony.com/doc/current/components/dependency_injection/introduction.html

### Consuming a service
```php
try {
    $result = $this->symfony2_bridge->getContainer()->get('my_service')->businessLogicServiceMethod('primityve parameter'));
} catch(Exception $e) {
    // Unable to initialize Symfony2 kernel
}
```
More information about SOA: http://en.wikipedia.org/wiki/Service-oriented_architecture

### Getting Doctrine2 entity manager
```php
try {
    $em = $this->symfony2_bridge->getContainer()->get('doctrine')->getManager();
} catch(Exception $e) {
    // Unable to initialize Symfony2 kernel
}
```

### Loading an arbitrary class defined within a Symfony2 bundle
```php
try {
    $container = $this->symfony2_bridge->getContainer(); // Initializes Symfony2 PSR class loader
    $imageHelper = new \Pepis\ImageManipulationBundle\Helpers\ImageHelper();
} catch(Exception $e) {
    // Unable to initialize Symfony2 kernel
}
```