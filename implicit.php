<?php 

require 'vendor/autoload.php';

/*
  1- Autocompletion. And knowing clearly what type is each vble
     NB Finds when looking for usages
 *  Services::getHeater if type is anotated in phpdoc it will make work autocompletion and find usages
 * 
  2- Lazy load: on construction 
 * 
  2b - Lazy load on function usage
    ServiceRef achieves lazy call. Considering the split of one class into two with deps that
 *  almost always uses seems a great solution as well. Eg: MyGoalService always uses all 
 *  of their deps, but LRS might use 1,2,3 or none deps on each request.
 *  Last, using Service::getUserService()->getUser() style of calls allow for autocompletion
 *  and lazyness as well. Though is not clear by reading the class what are their deps.
  3 - Avoid definition of the same fact repeated times
  4 - To some extent, is type safe
 * 
 * AutowiringContainer. 
 *    - phpDoc refs entries in Pimple as opposed to resolving interfaces/classes, because as the
 *      app grows a conflict resolution method would be needed and Pimple is just that.
 *      Pimple entries represent interfaces w/o the need of declaring them.
 *    - If name class and key in Pimple are different autocomplete will only work
 *      if an interface is created and pimple key matches interface.
 * NEXT
 * >>> 
 *    ? Discarded two traits, because of conflicting problems. Use inheritance overriding
 *      offsetGet. Organize code in two traits: autowire, serviceRef. 
 *    ? Inject static props
 *    ? Use namespaces
 *    ? will autowiring resolve circular deps
 *    ? new ReferenceContainer extends AutowiredContainer. Using extends to force always to be
 *          the first method in execute. No possibility though of not autowire
 *          test it if really enhance performance?
 *          supports @noRefs for classes for which we know wont benefit from it.
 *          Impl only after testing how the magic method in between affects performance
 *    ? trait to automatically resolve entries in container. Eg the case
 *          $['Heater'] = function () { return new Heater(); }
 *      Annotate classes as @singleton or @factory
 *    
 *      
*/


/*
Tries to follow the implicit declarations example of
 http://jonasboner.com/2008/10/06/real-world-scala-dependency-injection-di/

Because there is no implicit keyword in PHP, an alternative would be to use Pimple as a trait having all of the getServiceName() defined and inside the closures.
That way it gets autocompletion and lazyloading.
It however makes losing infomration about who depends on who, minor issue i think.

PHP doesnt allow modifying class code at runtime http://stackoverflow.com/questions/1593497/php-runtime-class-modification so an opportunity seems lost of achieving lazy function the by replacing getXX impl.

Having a external yaml is clumsy http://symfony.com/doc/current/book/service_container.html
*/

//---------------------------------------------------------------
trait OnOffDevice {
   public abstract function on();  
   public abstract function off();  
}

trait SensorDevice {
  public $isCoffeePresent;
}

//---------------------------------------------------------------
/**
 * A classic heater
 */
class Heater {
 
  public function on() {
     print "Heater is on \n";
  }

  public function off() {
     print "Heater is off \n";
  }
}

class PotSensor {
  public function __construct() {
    $this->isCoffeePresent = true;
  }
}

//---------------------------------------------------------------

  function p($anything) {
    print "Instance created \n";
    return $anything;
  }

  
  // makes sure class is only init on first function call
  class ServiceRef {
      private $container;
      private $serviceName;
      private $instance = null;
      
      public function __construct($c, $name) {
          $this->container = $c;
          $this->serviceName = $name;
      }
      
      public function __call($name, $arguments) {
          if ($this->instance == null) {
              $this->instance = $this->container[$this->serviceName];
          }
          call_user_func_array(array($this->instance, $name), $arguments);
      }
  }

  class S {
      public static $a = 1;
      
     /**
      * 
      * @return Heater
      */
     public static function getHeater() {
        static $sr = null;
        if ($sr == NULL) {
            $c = new Pimple();

            $c['PotSensor'] = function($c) {
             return p(new PotSensor());
            };

            $c['Heater'] = function($c) {
             return p(new Heater());
            };
            
            $sr = new ServiceRef($c, 'Heater');
        }
        return $sr;
    }
  }

//---------------------------------------------------------------

class Warmer {
  private $postSensor;
  
  /** @var Heater */
  private $heater;
  
  public function __construct() {
      print "Constructed Warmer \n";
  }
  
  public function findUsage() {
      $this->heater->on();
      print "After calling findUsage \n";
  }
}

//---------------------------------------------------------------

$w = autowire(new Warmer());
$w->findUsage();