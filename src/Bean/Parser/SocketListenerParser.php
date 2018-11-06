<?php

namespace Swoft\Socket\Bean\Parser;

use Swoft\Bean\Annotation\Scope;
use Swoft\Socket\Bean\Annotation\SocketListener;
use Swoft\Socket\Bean\Collector\SocketListenerCollector;

class SocketListenerParser
{
    /**
     * @param string      $className
     * @param SocketListener $objectAnnotation
     * @param string      $propertyName
     * @param string      $methodName
     * @param mixed       $propertyValue
     *
     * @return array
     */
    public function parser(string $className, $objectAnnotation = null, string $propertyName = '', string $methodName = '', $propertyValue = null)
    {
        $beanName = $className;
        $scope    = Scope::SINGLETON;

        SocketListenerCollector::collect($className, $objectAnnotation, $propertyName, $methodName, $propertyValue);

        return [$beanName, $scope, ''];
    }
}
