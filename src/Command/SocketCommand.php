<?php

namespace Swoft\Socket\Command;

use Swoft\Socket\Socket\SocketServer;
use Swoft\Console\Bean\Annotation\Command;

/**
 * The group command list of socket server
 * @Command(coroutine=false,server=true)
 */
class SocketCommand
{
    /**
     * start socket
     * @Usage {fullCommand} [-d|--daemon]
     * @Options
     *     -d, --daemon    Run server on the background
     * @Example
     *     {fullCommand}
     *     {fullCommand} -d
     */
    public function start()
    {
        $socketServer = $this->getSocketServer();
        // 是否正在运行
        if ($socketServer->isRunning()) {
            $serverStatus = $socketServer->getServerSetting();
            \output()->writeln("<error>The server have been running!(PID: {$serverStatus['masterPid']})</error>", true, true);
        }
        // 选项参数解析
        $this->setStartArgs($socketServer);
        $masterStatus = $socketServer->socketMasterSetting;
        // socket 启动参数
        $masterHost = $masterStatus['host'];
        $masterPort = $masterStatus['port'];
        $masterType = $masterStatus['type'];
        $masterMode = $masterStatus['mode'];
        $masterName = $masterStatus['name'];
        // 信息面板
        $lines = [
            '                    Information Panel                     ',
            '*************************************************************',
            "* $masterName server | Host: <note>$masterHost</note>, port: <note>$masterPort</note>, mode: <note>$masterMode</note>, type: <note>$masterType</note>",
            '*************************************************************',
        ];
        \output()->writeln(implode("\n", $lines));
        // 启动
        $socketServer->start();
    }

    /**
     * reload worker process
     *
     * @Usage
     *   {fullCommand} [arguments] [options]
     * @Options
     *   -t     Only to reload task processes, default to reload worker and task
     * @Example
     * php swoft.php socket:reload
     */
    public function reload()
    {
        $socketServer = $this->getSocketServer();

        // 是否已启动
        if (!$socketServer->isRunning()) {
            output()->writeln('<error>The server is not running! cannot reload</error>', true, true);
        }

        // 打印信息
        output()->writeln(sprintf('<info>Server %s is reloading ...</info>', input()->getFullScript()));

        // 重载
        $reloadTask = input()->hasOpt('t');
        $socketServer->reload($reloadTask);
        output()->writeln(sprintf('<success>Server %s is reload success</success>', input()->getFullScript()));
    }

    /**
     * stop socket server
     *
     * @Usage {fullCommand}
     * @Example {fullCommand}
     */
    public function stop()
    {
        $socketServer = $this->getSocketServer();

        // 是否已启动
        if (!$socketServer->isRunning()) {
            \output()->writeln('<error>The server is not running! cannot stop</error>', true, true);
        }

        // pid文件
        $serverStatus = $socketServer->getServerSetting();
        $pidFile = $serverStatus['pfile'];

        @unlink($pidFile);
        \output()->writeln(sprintf('<info>Swoft %s is stopping ...</info>', input()->getFullScript()));

        $result = $socketServer->stop();

        // 停止失败
        if (!$result) {
            \output()->writeln(sprintf('<error>Swoft %s stop fail</error>', input()->getFullScript()));
        }

        \output()->writeln(sprintf('<success>Swoft %s stop success</success>', input()->getFullScript()));
    }

    /**
     * restart socket server
     *
     * @Usage {fullCommand}
     * @Options
     *   -d, --daemon    Run server on the background
     * @Example
     *   {fullCommand}
     *   {fullCommand} -d
     */
    public function restart()
    {
        $socketServer = $this->getSocketServer();

        // 是否已启动
        if ($socketServer->isRunning()) {
            $this->stop();
        }

        // 重启默认是守护进程
        $socketServer->setDaemonize();
        $this->start();
    }

    /**
     * @return SocketServer
     */
    private function getSocketServer(): SocketServer
    {
        $script = \input()->getScript();
        $socketServer = new SocketServer();
        $socketServer->setScriptFile($script);

        return $socketServer;
    }

    /**
     * @param SocketServer $socketServer
     */
    private function setStartArgs(SocketServer $socketServer)
    {
        if (\input()->getSameOpt(['d', 'daemon'], false)) {
            $socketServer->setDaemonize();
        }
    }
}