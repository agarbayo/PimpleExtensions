<?php

namespace PimpleContainers;

class AutowiredContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testAutomaticInjection()
    {
        $c = new \PimpleContainers\AutowiredContainer();
        $c['ServiceA'] = function () {
            return new \PimpleContainers\Fixtures\ServiceA();
        };
        $c['ServiceB'] = new \PimpleContainers\Fixtures\ServiceB();
        
        
        $serviceA = $c['ServiceA'];
        $this->assertInstanceOf('\PimpleContainers\Fixtures\ServiceB', $serviceA->getServiceB());
        $this->assertInstanceOf('\PimpleContainers\Fixtures\ServiceB', $serviceA->getServiceB2());
        $this->assertInstanceOf('\PimpleContainers\Fixtures\anotherNamespace\ServiceC', $serviceA->getServiceC());
        
        $this->assertEquals('value already assigned', $serviceA->getServiceBNotInjected());
        $this->assertNull($serviceA->getNotInjectedJustAComment());
        $this->assertNull($serviceA->getNotInjectedLacksDocComment());
    }
    
    public function testFactory()
    {
        $c = new \PimpleContainers\AutowiredContainer();
        $c['ServiceA'] = $c->factory(function() {
            return new \PimpleContainers\Fixtures\ServiceA();
        }); 
        $c['ServiceB'] = new \PimpleContainers\Fixtures\ServiceB();
        
        $serviceA1 = $c['ServiceA'];   
        $serviceA2 = $c['ServiceA'];
        
        $this->assertInstanceOf('\PimpleContainers\Fixtures\ServiceB', $serviceA1->getServiceB());
        $this->assertInstanceOf('\PimpleContainers\Fixtures\ServiceB', $serviceA2->getServiceB());
    }
}