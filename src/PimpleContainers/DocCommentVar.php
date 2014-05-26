<?php

namespace PimpleContainers;

/**
 * Parse the full name of a var in a DocComment
 */
class DocCommentVar {
    
    /** @var string */
    private $docComment;
    
    /** @var \ReflectionProperty p */
    private $p;
    
    public function __construct(\ReflectionProperty $p) {
        $this->p = $p;
        $this->docComment = $p->getDocComment();
    }
    
    /**
     * @returns string|null Full class name referenced in the comment. Null
     */
    public function getClass() {
        if (empty($this->docComment)) {
            return null;
        }
        
        $className = $this->parseClass($this->docComment);
        if($className==null || class_exists($className)) {
            return $className; 
        }
        
        // Try with declaring class namespace
        $ns = $this->p->getDeclaringClass()->getNamespaceName();
        $cNameNS = $ns.'\\'.$className;
        if (!class_exists($cNameNS)) {
            $cNameNS = $this->findClassNameInImports($className);
        }
        
        // Try to find class inside the imports
        return $this->fullQualified($cNameNS);
    }
    
    private function fullQualified($cName) {
        return (strpos($cName, '\\') === 0)?$cName:'\\'.$cName;
    }
    
    private function parseClass($docComment) {
        preg_match("/(.*)@var ([^\s]*) (.*)/", $this->docComment, $outputArray);
        return isset($outputArray[2])?$outputArray[2]:null;
    }
    
    private function findClassNameInImports($className) {
        $namespaces = $this->getImportedNamespaces();
        foreach ($namespaces as $alias => $namespace) {
            if($alias == strtolower($className)) {
                return $namespace;
            }
        }
        return null;
    }
    
    /**
     * Uses https://github.com/doctrine/common/blob/master/lib/Doctrine/Common/Reflection/StaticReflectionParser.php
     * alt: https://github.com/Andrewsville/PHP-Token-Reflection/tree/master
     * 
     * @return string[string] Keys is class name or alias and values are full class names
     */
    private function getImportedNamespaces() {
        //https://github.com/doctrine/common/blob/master/lib/Doctrine/Common/Reflection/StaticReflectionParser.php
        $paths = include 'vendor/composer/autoload_namespaces.php';
        $finder = new \Doctrine\Common\Reflection\Psr0FindFile($paths);
        $c = $this->p->getDeclaringClass()->getName();
        $parser = new \Doctrine\Common\Reflection\StaticReflectionParser($c, $finder);
        return $parser->getUseStatements();
    }
}
