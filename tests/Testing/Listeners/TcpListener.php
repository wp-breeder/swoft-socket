<?php
/**
 * Created by PhpStorm.
 * User: WP
 * Date: 2018/11/3
 * Time: 11:32
 */

namespace SwoftTest\Socket\Testing\Listeners;

use Swoft\Bootstrap\Listeners\Interfaces\CloseInterface;
use Swoft\Bootstrap\Listeners\Interfaces\ConnectInterface;
use Swoft\Bootstrap\Listeners\Interfaces\ReceiveInterface;
use Swoft\Socket\Bean\Annotation\SocketListener;
use Swoft\Socket\Event\SwooleEvent;
use Swoole\Server;

/**
 * Class SocketListenerTest
 * @package SwoftTest\Socket\Testing\Listeners
 * @SocketListener({
 *     SwooleEvent::ON_CONNECT,
 *     SwooleEvent::ON_RECEIVE,
 *     SwooleEvent::ON_CLOSE,
 * },
 *     name="tcp"
 * )
 */
class TcpListener implements ReceiveInterface, CloseInterface, ConnectInterface
{
    public function onConnect(Server $server, int $fd, int $reactorId)
    {
        echo "tcp connect" . PHP_EOL;
    }

    public function onReceive(Server $server, int $fd, int $reactorId, string $data)
    {
        echo "tcp receive data: " . $data . PHP_EOL;
    }

    public function onClose(Server $server, int $fd, int $reactorId)
    {
        echo "tcp close" . PHP_EOL;
    }

}