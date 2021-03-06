<?php

namespace PimpleContainers\Fixtures;

use PimpleContainers\Fixtures\anotherNamespace\ServiceC;

use PimpleContainers\Fixtures\anotherNamespace\ServiceC as ServiceAliased;

/**
 * Dummy service for testing injection
 */
class ServiceA {
    
    /** @var \PimpleContainers\Fixtures\ServiceB An instance from serviceB will be injected */
    private $serviceB;
    
    /** @var ServiceB  Injects on shortName in this namespace */
    private $serviceB2;
    
    /** @var ServiceC  Injects shortName imported with use keyword*/
    private $serviceC;
    
    /** @var ServiceAliased */
    private $serviceAliased;
    
    /* *************************************************************************
     * Cases where nothing is injected
     ************************************************************************* */
    
    /** @var ServiceB  Only null values after construction get injected */
    private $serviceBNotInjected;
    
    /** Won't inject, this is just a comment */
    private $notInjectedJustAComment;

    private $notInjectedLacksDocComment;
    
    
    
    
    public function __construct() {
        $this->serviceBNotInjected = "value already assigned";
    }
    
    public function getServiceB() {
        return $this->serviceB;
    }
    
    public function getServiceB2() {
        return $this->serviceB2;
    }
    
    public function getServiceC() {
        return $this->serviceC;
    }
    
    public function getServiceAliased() {
        return $this->serviceAliased;
    }
    
    public function getServiceBNotInjected() {
        return $this->serviceBNotInjected;
    }
    
    public function getNotInjectedJustAComment() {
        return $this->notInjectedJustAComment;
    }

    public function getNotInjectedLacksDocComment() {
        return $this->notInjectedLacksDocComment;
    }
    
}
