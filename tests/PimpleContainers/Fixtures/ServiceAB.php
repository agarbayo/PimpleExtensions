<?php

namespace PimpleContainers\Fixtures;


/**
 * Dummy service to test cyclic dependencies
 */
class ServiceAB {
    
    
    /** @var \PimpleContainers\Fixtures\ServiceBA */
    private $serviceBA;
    
    
    public function getServiceBA() {
        return $this->serviceBA;
    }
}
