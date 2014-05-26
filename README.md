
Pimple extension that uses php doc annotations to automatically resolves and inject service dependencies.

## Example

```php
class ServiceA {
    /** @var ServiceB */
    private $serviceB;

    public function getServiceB() {
        return $this->serviceB;
    }
}

$c = new \PimpleContainers\AutowiredContainer();
$c['ServiceA'] = function () {
    return new \PimpleContainers\Fixtures\ServiceA();
};
$c['ServiceB'] = function () {
  new \PimpleContainers\Fixtures\ServiceB();
};


$serviceA = $c['ServiceA'];//The container took care of adding an instance of serviceB
$serviceB = $serviceA->getServiceB();

```

## Automatically added singleton services

The above example could be rewritten as:
```php
$c = new \PimpleContainers\AutowiredContainer();
$serviceA = $c['ServiceA'];
$serviceB = $serviceA->getServiceB();
```
To avoid writing boilerplate, when services are not found in the container, it assumes
the convention that a singleton with the short class name should be exists and adds it
to the container.

More examples of usage found in the unit tests.

## Random Notes

- Avoids external configuration file as in http://symfony.com/doc/current/book/service_container.html
- Services are injected after constructor execution. Only null properties with valid type annotation are injected
- [Nice overview of different approaches to DI in Scala](http://jonasboner.com/2008/10/06/real-world-scala-dependency-injection-di/)


## TODO
- Fix composer.json
- Support better coding to interfaces
- Circular deps?
- @factory for classes
- Merge with lazyref branch