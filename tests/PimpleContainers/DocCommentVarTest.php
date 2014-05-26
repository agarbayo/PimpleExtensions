<?php

namespace PimpleContainers;

class DocCommentVarTest extends \PHPUnit_Framework_TestCase {

    /**
     * @param string $classNameA
     * @param string $propertyName
     * @return \ReflectionProperty 
     */
    private function getReflectedClassProperty($className, $propertyName) {
        $obj = new $className();
        $rClass = new \ReflectionClass($obj);
        $p = $rClass->getProperty($propertyName);
        $p->setAccessible(true);
        return $p;
    }

    /**
     * @dataProvider cases
     * 
     * @param string $expectedClassName
     * @param string $className
     * @param string $propName
     */
    public function testParseClass($expectedClassName, $className, $propName) {
        $p = $this->getReflectedClassProperty($className, $propName);
        $var = new DocCommentVar($p);
        
        $this->assertEquals($expectedClassName, $var->getClass());
    }

    public function cases() {
        $serviceBClassName = '\PimpleContainers\Fixtures\ServiceB';
        $serviceAClassName = '\PimpleContainers\Fixtures\ServiceA';
        return [
            //    Expected            Class name          Property
            array($serviceBClassName, $serviceAClassName, 'serviceB'),
            array($serviceBClassName, $serviceAClassName, 'serviceB2'),
            array('\PimpleContainers\Fixtures\anotherNamespace\ServiceC', $serviceAClassName, 'serviceC'),
            array('\PimpleContainers\Fixtures\anotherNamespace\ServiceC', $serviceAClassName, 'serviceAliased'),
            //Invalid cases
            array(null, $serviceAClassName, 'notInjectedJustAComment'),
            array(null, $serviceAClassName, 'notInjectedLacksDocComment'),
        ];
    }
}
