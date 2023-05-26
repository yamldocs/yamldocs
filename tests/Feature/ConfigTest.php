<?php

use Yamldocs\YamlConfig;

test('YAML config is loaded', function () {
    $config = new YamlConfig();
    $config->load(__DIR__ . '/../Assets/config.yaml');

    $this->assertArrayHasKey('app_path', $config->parameters);
    $this->assertArrayHasKey('builders', $config->parameters);
});
