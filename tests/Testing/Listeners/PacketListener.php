<?php
/**
 * Created by PhpStorm.
 * User: WP
 * Date: 2018/11/5
 * Time: 18:56
 */

namespace SwoftTest\Socket\Testing\Listeners;

use Swoft\Socket\Event\SwooleEvent;
use Swoft\Bootstrap\Listeners\Interfaces\PacketInterface;
use Swoft\Socket\Bean\Annotation\SocketListener;
use Swoole\Server;

/**
 * Class PacketListener
 * @package SwoftTest\Socket\Testing\Listeners
 * @SocketListener(event={SwooleEvent::ON_PACKET}, name="udp")
 */
class PacketListener implements PacketInterface
{
    public function onPacket(Server $server, string $data, array $clientInfo)
    {
        echo "udp receive data: " . $data . PHP_EOL;
    }
}