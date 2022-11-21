<?php

use Minicli\App;
use Minicli\Command\CommandCall;
use App\Document;

function getCommandsPath(): string
{
    return __DIR__ . '/../app/Command';
}

function getApp(): App
{
    $config = [
        'app_path' => getCommandsPath()
    ];

    return new App($config);
}

function getProdApp(): App
{
    $config = [
        'app_path' => getCommandsPath(),
        'debug' => false
    ];

    return new App($config);
}

function getCommandCall(array $parameters = null): CommandCall
{
    return new CommandCall(array_merge(['minicli'], $parameters));
}

function getDocument(): Document
{
    return new Document(__DIR__ . '/Assets/test.yaml', __DIR__ . '/../templates');
}
