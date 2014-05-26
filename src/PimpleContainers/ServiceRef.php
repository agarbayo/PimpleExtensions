<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PimpleContainers;

// makes sure class is only init on first function call
class ServiceRef {
    private $container;
    
    /* @var string */
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
