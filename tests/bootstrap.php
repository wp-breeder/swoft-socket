<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  wp_breeder@163.com
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */
require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';
require_once dirname(dirname(__FILE__)) . '/tests/config/define.php';

// init
\Swoft\App::$isInTest = true;

\Swoft\Bean\BeanFactory::init();

/* @var \Swoft\Bootstrap\Boots\Bootable $bootstrap*/
$bootstrap = \Swoft\App::getBean(\Swoft\Bootstrap\Bootstrap::class);
$bootstrap->bootstrap();

$console = new \Swoft\Console\Console();
$console->run();

\Swoft\Bean\BeanFactory::reload([
    'application' => [
        'class'  => \Swoft\Testing\Application::class,
        'inTest' => true
    ],
]);
$initApplicationContext = new \Swoft\Core\InitApplicationContext();
$initApplicationContext->init();
