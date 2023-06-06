<?php

use Yamldocs\Builder\DefaultBuilder;
use Yamldocs\Builder\TestBuilder;
use Yamldocs\BuilderService;

test('Builder Service is loaded within app', function() {
    $app = getApp();
    $builder = $app->builder;
    $this->assertInstanceOf(BuilderService::class, $builder);
});

test('Builder Service loads builders from config.yaml file', function() {
    $app = getApp();
    $this->assertInstanceOf(DefaultBuilder::class, $app->builder->getBuilder('default'));
    $this->assertInstanceOf(TestBuilder::class, $app->builder->getBuilder('test'));
});

test('Builder Service loads custom builder config options', function() {
    $app = getApp(__DIR__ . '/../Assets/config_alt.yaml');
    $defaultBuilder = $app->builder->getBuilder('default');

    expect($defaultBuilder->builderOptions)->not()->toBeEmpty()
        ->and($defaultBuilder->templatesDir)->toContain("templates/default");
});