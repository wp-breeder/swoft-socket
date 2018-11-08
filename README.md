## swoft-socket

### 说明

  基于 **[swoft](https://github.com/swoft-cloud/swoft)** 的 `socket` 组件，支持 `tcp/udp` 多端口监听，详细内容参考[swoole wiki](https://wiki.swoole.com/wiki/page/161.html)。


### 安装

1.  composer command

```shell
    composer require wp-breeder/swoft-socket    
```

### 快速开始

1. 在 `/project-to-path/config/properties/app` 中添加配置:

```php
    // 配置启动扫描
    'bootScan' => [
        // code...
        // 把 sockerListener 注解的目录添加到启动扫描
        'App\Listener'
    ],
    // 指定扫描组件
    'components' => [
            'custom' => [
                'Swoft\\Socket\\'
            ],
        ],
```

2. 在 `/project-to-path/config/server` 中添加配置:

```php
    //for socket component
    'server' => [
            //code ...
            // socketable 配置是否开启socket监听
            'socketable' => env('SOCKETABLE', false),
           
        ],
    
    // for socket component test
        'socket' => [
            [
                'host' => env('SOCKET_TCP_HOST', '0.0.0.0'),
                'port' => env('SOCKET_TCP_PORT', 8010),
                'mode' => env('SOCKET_TCP_MODE', SWOOLE_PROCESS),
                'type' => env('SOCKET_TCP_TYPE', SWOOLE_SOCK_TCP),
                //server 名称,必须设置，如果未设置则不监听(用于区分不同server 设置不同的回调，如果重复，则会覆盖)
                //如果是单独开启，第一个不设置name则会报错
                'name' => 'tcp'
            ],
            [
                'host' => env('SOCKET_UDP_HOST', '0.0.0.0'),
                'port' => env('SOCKET_UDP_PORT', 8011),
                'mode' => env('SOCKET_UDP_MODE', SWOOLE_PROCESS),
                'type' => env('SOCKET_UDP_TYPE', SWOOLE_SOCK_UDP),
                'name' => 'udp'
            ],
        ],
```

### 用法

1. 与 `rpc`、`http` 混合使用

>新增注解 `@SocketListener` , 使用方法大致与 `@ServerListener` 一致，不同是需要指定该监听器监听的端口。
  
```php
    /**
     *   监听多个事件
     * @SocketListener({
     *     SwooleEvent::ON_CONNECT,
     *     SwooleEvent::ON_RECEIVE,
     *     SwooleEvent::ON_CLOSE,
     * },
     *   监听端口的名称，与socket配置文件中的name对应(如重复则最后一个生效，不存在则监听不生效)
     *     name="tcp"
     * )
```

2. 单独开启，与 `rpc` 、 `http` 、 `ws` 一致。(若监听多个，默认第一个配置为master server, 其他配置单独监听指定端口)

```shell
    php bin/swoft socket:start
```


3. 使用示例：

```php
namespace SwoftTest\Socket\Testing\Listeners;

use Swoft\Bootstrap\Listeners\Interfaces\CloseInterface;
use Swoft\Bootstrap\Listeners\Interfaces\ConnectInterface;
use Swoft\Bootstrap\Listeners\Interfaces\ReceiveInterface;
use Swoft\Socket\Bean\Annotation\SocketListener;
use Swoft\Socket\Event\SwooleEvent;
use Swoole\Server;

/**
 * tcp 监听 demo
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

```

```php
namespace SwoftTest\Socket\Testing\Listeners;

use Swoft\Socket\Event\SwooleEvent;
use Swoft\Bootstrap\Listeners\Interfaces\PacketInterface;
use Swoft\Socket\Bean\Annotation\SocketListener;
use Swoole\Server;

/**
 * udp 监听 demo
 * Class PacketListener
 * @package SwoftTest\Socket\Testing\Listeners
 * @SocketListener(event={SwooleEvent::ON_PACKET}, name="udp")
 */
class PacketListener implements PacketInterface
{
    public function onPacket(Server $server, string $data, array $clientInfo)
    {
        echo "udp receive data: " . $data;
    }
}
```

### LICENSE

The Component is open-sourced software licensed under the [Apache license](https://github.com/wp-breeder/swoft-socket/blob/master/LICENSE).
