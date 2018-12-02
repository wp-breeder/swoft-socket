<?php

namespace Swoft\Socket\Socket;

use Swoft\App;
use Swoft\Bean\Collector\SwooleListenerCollector;
use Swoft\Bootstrap\Server\AbstractServer;
use Swoft\Socket\Bean\Collector\SocketListenerCollector;
use Swoft\Socket\Event\SwooleEvent;
use Swoole\Server;

class SocketServer extends AbstractServer
{
    /**
     * socket settings
     * @var array
     */
    public $socketSettings = [];

    public $socketMasterSetting = [];

    public function __construct()
    {
        parent::__construct();

        // init socket server
        $settings = self::initSocketConfig();

        $this->socketSettings = $settings;

        $this->socketMasterSetting = \array_shift($this->socketSettings);
    }

    /**
     * Start server
     */
    public function start()
    {
        $this->server = new Server(
            $this->socketMasterSetting['host'],
            $this->socketMasterSetting['port'],
            $this->socketMasterSetting['mode'],
            $this->socketMasterSetting['type']
        );
        $serverName = $this->socketMasterSetting['name'];
        // Bind event callback
        $listenMasterSetting = self::getListenSocketSetting($this->socketMasterSetting);
        $setting = \array_merge($this->setting, $listenMasterSetting);
        $this->server->set($setting);
        $this->server->on(SwooleEvent::ON_START, [$this, 'onStart']);
        $this->server->on(SwooleEvent::ON_WORKER_START, [$this, 'onWorkerStart']);
        $this->server->on(SwooleEvent::ON_MANAGER_START, [$this, 'onManagerStart']);
        $this->server->on(SwooleEvent::ON_PIPE_MESSAGE, [$this, 'onPipeMessage']);
        $socketEvents = self::getSocketEvents($serverName);
        $swooleEvents = $this->getSwooleEvents();
        $this->registerSocketSwooleEvents($this->server, \array_merge($swooleEvents, $socketEvents));
        // before start
        $this->beforeServerStart();
        $this->server->start();
    }

    /**
     * init socket settings
     * @return array
     */
    public static function initSocketConfig(): array
    {
        //get socket config
        $settings = App::getAppProperties()->get('server.socket');

        if (empty(\reset($settings)) || !\is_array($settings)) {
            throw new \InvalidArgumentException('Socket startup parameters is not configured, settings=' . \json_encode($settings));
        }

        return $settings;
    }

    /**
     * get listen settings
     * @param array $settings
     * @return array
     */
    public static function getListenSocketSetting(array $settings): array
    {
        unset($settings['host'], $settings['port'], $settings['mode'], $settings['type'], $settings['name']);
        return $settings;
    }

    /**
     * register socket swoole events
     * @param Server $handler
     * @param array $events
     */
    public static function registerSocketSwooleEvents($handler, array $events)
    {
        foreach ($events as $event => $beanName) {
            $object = bean($beanName);
            $method = SwooleEvent::getHandlerFunction($event);
            $handler->on($event, [$object, $method]);
        }
    }

    /**
     * get socket listener events
     * @param string $serverName
     * @return array
     */
    public static function getSocketEvents(string $serverName): array
    {
        $socketListeners = SocketListenerCollector::getCollector();
        $socketListener = $socketListeners[$serverName] ?? [];

        return $socketListener;
    }

    /**
     * get swoole listener events
     * @return array
     */
    private function getSwooleEvents():array
    {

        $swooleListeners = SwooleListenerCollector::getCollector();
        $portEvents = $swooleListeners[SwooleEvent::TYPE_PORT][0] ?? [];
        $serverEvents = $swooleListeners[SwooleEvent::TYPE_SERVER] ?? [];
        return \array_merge($portEvents, $serverEvents);
    }
}
