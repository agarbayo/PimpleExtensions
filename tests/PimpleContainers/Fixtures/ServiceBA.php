<?php

namespace PimpleContainers\Fixtures;


/**
 * Dummy service to test cyclic dependencies
 */
class ServiceBA {
    
    
    /** @var \PimpleContainers\Fixtures\ServiceAB */
    private $serviceAB;
    
    
    public function getServiceAB() {
        return $this->serviceAB;
    }
}
