<?php

namespace PimpleContainers;

/**
 * Notes: 
 * 
 * -Constructor code cannot call dependencies because they are resolved
 * after constructor has been called. Should be ok:
 * http://misko.hevery.com/code-reviewers-guide/flaw-constructor-does-real-work/
 * 
 * 
 * 
 * @author Angel Garbayo
 */
class AutowiredContainer extends \Pimple\Container {
    
    /** @var \SplObjectStorage Bypass injections for some callables: factory, protected */
    private $notInjected;
    
    /** @var \SplObjectStorage Keep track of factories. Duplicate from /Pimple/Container where is private */
    private $factories;
    
    private $notInjectedValues = array();
    
    public function __construct(array $values = array()) {
        parent::__construct($values);
        $this->notInjected = new \SplObjectStorage();
        $this->factories   = new \SplObjectStorage();
    }
    
    public function offsetSet($id, $value) {
        // First conditions needed to track cases of factory/protected which later migh be extended
        if (
                (is_object($value) || method_exists($value, '__invoke')) 
                && (isset($this->notInjected[$value])|| isset($this->notInjectedValues[$id]))
            ) {
            $this->notInjectedValues[$id] = $value;
        } else if (is_object($value) && method_exists($value, '__invoke')) {
            $value = $this->addClosure($value);
        }
        parent::offsetSet($id, $value);
    }
    
    public function offsetGet($id) {
        $value = parent::offsetGet($id);
        if ($this->isFactoryId($id)) {
            $this->injectProperties($value);
        }
        return $value;
    }
    
    private function isFactoryId($id) {
        if(isset($this->notInjectedValues[$id])) {
            $value = $this->notInjectedValues[$id];
        }
        return (isset($value))?isset($this->factories[$value]):false;
    }

    function addClosure($factory) {
        $callable = function ($value, $c) {
            if (is_object($value)) {
                $this->injectProperties($value);
            }
            return $value;
        };
                
        $evalue = function ($c) use ($callable, $factory) {
            return method_exists($factory, '__invoke')?$callable($factory($c), $c):$callable($factory, $c);
        };
        
        return $evalue;
    }

    private function injectProperties($obj) {
        $reflect = new \ReflectionClass($obj);
        $allProps = $reflect->getProperties(\ReflectionProperty::IS_PRIVATE | \ReflectionProperty::IS_PROTECTED);
        
        // Only inject properties that lack a value
        $props = array_filter($allProps, function($p) use ($obj) {
            $p->setAccessible(true);
            $value = $p->getValue($obj);
            return $value == null;
        });
        
        array_walk($props, ['\PimpleContainers\AutowiredContainer', 'injectProperty'], $obj);
    }
    
    private function injectProperty(\ReflectionProperty $p, $idx, $obj) {
        $docVar = new DocCommentVar($p);
        $className = $docVar->getClass();
        if ($className != null) {
            $classInstance = $this->getInstance($className);
            $p->setValue($obj, $classInstance);
        }
    }
    
    /**
     * Looks in the container for an instance of this class. If it doesnt exist,
     * it creates a new instance and adds it to the container.
     * 
     * @param string $className
     * @return mixed
     */
    private function getInstance($className) {
        // Try in container with className as is
        if (isset($this[$className])) {
            return $this[$className];
        }
        
        // Try in conatainer with shortName
        $tokens = split("\\\\", $className);
        $shortName = array_pop($tokens);
        if (isset($this[$shortName])) {
            return $this[$shortName];
        } 
        
        // Cannot be found, create new instance and add it to container
        $this[$shortName] = new $className();
        return $this[$shortName];
    }
    
    public function factory($callable) {
        $ret = parent::factory($callable);
        $this->notInjected->attach($callable);
        $this->factories->attach($callable);
        return $ret;
    }
    
    public function protect($callable) {
        $ret = parent::protect($callable);
        $this->notInjected->attach($callable);
        return $ret;
    }
    
    public function extend($id, $callable) {
        $ret = parent::extend($id, $callable);
        
        if (isset($this->notInjectedValues[$id])) {
            $factory = $this->notInjectedValues[$id];

            if (isset($this->notInjected[$factory])) {
                $this->notInjected->detach($factory);
                $this->notInjected->attach($ret);
            }
        }
        return $ret;
    }
    
}