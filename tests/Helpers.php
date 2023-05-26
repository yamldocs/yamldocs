<?php

use Minicli\App;
use Minicli\Command\CommandCall;
use Minicli\Config;
use Yamldocs\BuilderService;
use Yamldocs\Document;
use Yamldocs\YamlConfig;

function getCommandsPath(): string
{
    return __DIR__ . '/../app/Command';
}

function getConfigAsArray(): array
{
    $config = new YamlConfig();
    $config->load(__DIR__ . '/../config.yaml');
    $config->set('app_path', getCommandsPath());
    return $config->parameters;
}

function getApp(): App
{
    $app = new App(getConfigAsArray());
    $app->addService('builder', new BuilderService());

    return $app;
}

function getDocument(): Document
{
    return new Document(__DIR__ . '/Assets/test.yaml', __DIR__ . '/../templates');
}
