<?php
/**
 * Created by PhpStorm.
 * User: WP
 * Date: 2018/11/5
 * Time: 21:15
 */

namespace Swoft\Socket\Bootstrap\Listeners;

use Swoft\Bean\Annotation\BeforeStart;
use Swoft\Bootstrap\Listeners\Interfaces\BeforeStartInterface;
use Swoft\Bootstrap\Server\AbstractServer;
use Swoft\Socket\Socket\SocketServer;
use Swoole\Server;

/**
 * listen to other ports
 * Class BeforeStartEventListener
 * @package Swoft\Socket\Bootstrap\Listeners
 * @BeforeStart()
 */
class BeforeStartEventListener implements BeforeStartInterface
{
    private $serverList = [];

    public function onBeforeStart(AbstractServer $server)
    {
        // start socket listen
        if ((int)$server->serverSetting['socketable'] === 1) {
            if (!isset($server->socketSettings)) {
                $server->socketSettings = SocketServer::initSocketConfig();
            }
            $this->listenSocketPort($server, $server->socketSettings);
        }
    }

    /**
     * listen socket
     * @param AbstractServer $server
     * @param array $socketSettings
     */
    private function listenSocketPort(AbstractServer $server, array $socketSettings)
    {

        /** @var Server $handler */
        $handler = $server->getServer();
        // listen port and set callback
        foreach ($socketSettings as $socketSetting) {
            $swooleEvents = SocketServer::getSocketEvents($socketSetting['name']);
            if (!empty($swooleEvents)) {
                $port = $handler->addlistener(
                    $socketSetting['host'],
                    $socketSetting['port'],
                    $socketSetting['type']
                );
                $this->setServerList($socketSetting);
                $listenSetting = SocketServer::getListenSocketSetting($socketSetting);
                $setting = \array_merge($server->setting, $listenSetting);
                $port->set($setting);
                SocketServer::registerSocketSwooleEvents($port, $swooleEvents);
            }

        }

        $this->showServerList();

    }


    /**
     * set server information
     * @param array $socketSettings
     */
    private function setServerList(array $socketSettings)
    {
        $serverHost = $socketSettings['host'];
        $serverPort = $socketSettings['port'];
        $serverType = $socketSettings['type'];
        $serverMode = $socketSettings['mode'];
        $serverName = $socketSettings['name'];

        $this->serverList[] =  "  $serverName server | Host: $serverHost, port: $serverPort, mode: $serverMode, type: $serverType";
    }

    /**
     * show server list
     */
    private function showServerList()
    {
        // server dashboard
        $lines = [
            '                        Server list                       ',
            ' ----------------------------------------------------------',
        ];
        $lines = \array_merge($lines, $this->serverList);

        \output()->writeln(implode("\n", $lines));
    }

}
