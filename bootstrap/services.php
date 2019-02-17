<?php
declare(strict_types=1);

use App\Services\Configuration\ConfigHelper;
use App\Services\Configuration\Interfaces\ConfigHelperInterface;
use Dotenv\Dotenv;
use Psr\Container\ContainerInterface;

return [
    ContainerInterface::class => function (ContainerInterface $container) {
        return $container;
    },
    ConfigHelperInterface::class => function (): ConfigHelperInterface {
        return new ConfigHelper(\implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'config']));
    },
    Dotenv::class => function () {
        $dotenv = new Dotenv(__DIR__ . '/../');
        $dotenv->load();

        return $dotenv;
    }
];
