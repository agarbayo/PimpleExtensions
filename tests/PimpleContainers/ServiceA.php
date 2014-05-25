<?php

namespace PimpleContainers;

use PimpleContainers\anotherNamespace\ServiceC;

/**
 * Dummy service for testing injection
 */
class ServiceA {
    
    /** @var \PimpleContainers\ServiceB An instance from serviceB will be injected */
    private $serviceB;
    
    /** @var ServiceB  Injects on shortName in this namespace */
    private $serviceB2;
    
    /** @var ServiceC  Injects shortName imported with use keyword*/
    private $serviceC;
    
    // TODO Test case with aliases
    
    
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
