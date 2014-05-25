<?php

namespace PimpleContainers;

class AutowiredContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testAutomaticInjection()
    {
        $c = new \PimpleContainers\AutowiredContainer();
        $c['ServiceA'] = function () {
            return new \PimpleContainers\ServiceA();
        };
        $c['ServiceB'] = new \PimpleContainers\ServiceB();
        
        
        $serviceA = $c['ServiceA'];
        var_dump($serviceA);
        $this->assertInstanceOf('\PimpleContainers\ServiceB', $serviceA->getServiceB());
        $this->assertInstanceOf('\PimpleContainers\ServiceB', $serviceA->getServiceB2());
        $this->assertInstanceOf('\PimpleContainers\anotherNamespace\ServiceC', $serviceA->getServiceC());
        
        $this->assertEquals('value already assigned', $serviceA->getServiceBNotInjected());
        $this->assertNull($serviceA->getNotInjectedJustAComment());
        $this->assertNull($serviceA->getNotInjectedLacksDocComment());
    }
}