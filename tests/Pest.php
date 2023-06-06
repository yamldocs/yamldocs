<?php

use Minicli\App;
use Minicli\Command\CommandCall;
use Minicli\Config;
use Yamldocs\BuilderService;
use Yamldocs\Document;
use Yamldocs\YamldocsConfig;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

// uses(Tests\TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function getCommandsPath(): string
{
    return __DIR__ . '/../app/Command';
}

function getConfigAsArray(?string $configFile = null): array
{
    $config = new YamldocsConfig();
    if ($configFile === null) {
        $configFile = __DIR__ . '/Assets/config.yaml';
    }
    $config->load($configFile);
    $config->set('app_path', getCommandsPath());
    $config->set('app_root', __DIR__ . '/Assets');
    return $config->parameters;
}

function getApp(?string $configFile = null): App
{
    $config = new YamldocsConfig();
    if ($configFile === null) {
        $configFile = __DIR__ . '/Assets/config.yaml';
    }
    $app = new App(getConfigAsArray($configFile));
    $app->addService('builder', new BuilderService());

    return $app;
}

function getDocument(): Document
{
    return new Document(__DIR__ . '/Assets/test.yaml', __DIR__ . '/../templates');
}