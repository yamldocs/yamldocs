<?php

test('command "build" is correctly loaded', function () {
    $app = getApp();
    $app->runCommand(['yamldocs', 'build']);
})->expectOutputRegex("/markdown/");
