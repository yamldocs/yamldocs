<?php

test('default command "demo" is correctly loaded', function () {
    $app = getApp();
    $app->runCommand(['minicli', 'demo']);
})->expectOutputRegex("/help/");

test('the "demo test" command echoes command parameters', function () {
    $app = getApp();
    $app->runCommand(['minicli', 'demo', 'test', 'user=erika']);
})->expectOutputRegex("/erika/");

test('the "demo table" command prints test table', function () {
    $app = getApp();
    $app->runCommand(['minicli', 'demo', 'table']);
})->expectOutputRegex("/Header 3/");
