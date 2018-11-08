<?php

namespace Swoft\Socket\Bean\Collector;

use Swoft\Bean\Annotation\BeforeStart;
use Swoft\Bean\CollectorInterface;
use Swoft\Socket\Bean\Annotation\SocketListener;
use Swoft\Socket\Event\SwooleEvent;

/**
 * Socket listener
 */
class SocketListenerCollector implements CollectorInterface
{
    /**
     * @var array
     */
    private static $listeners = [];

    /**
     * @param string $className
     * @param object $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param null $propertyValue
     * @return void
     */
    public static function collect(string $className, $objectAnnotation = null, string $propertyName = '', string $methodName = '', $propertyValue = null)
    {
        if ($objectAnnotation instanceof BeforeStart) {
            self::$listeners[SwooleEvent::ON_BEFORE_START][] = $className;
        } elseif ($objectAnnotation instanceof SocketListener) {
            $events = $objectAnnotation->getEvent();
            $serverName = $objectAnnotation->getName();

            foreach ($events as $event) {
                self::$listeners[$serverName][$event] = $className;
            }
        }
    }

    /**
     * @return array
     */
    public static function getCollector()
    {
        return self::$listeners;
    }
}
