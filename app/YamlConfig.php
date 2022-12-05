<?php

namespace App;

use Symfony\Component\Yaml\Yaml;

class YamlConfig
{
    public string $filePath;

    public array $parameters = [];

    public function load(string $filePath): void
    {
        $this->filePath = $filePath;
        $this->parameters = Yaml::parseFile($filePath);
    }

    public function merge(string $filePath): void
    {
        $this->parameters = array_merge($this->parameters, Yaml::parseFile($filePath));
    }

    public function set(string $key, mixed $value): void
    {
        $this->parameters[$key] = $value;
    }

    public function get(string $key): mixed
    {
        return $this->parameters[$key] ?? null;
    }
}