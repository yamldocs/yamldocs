<?php

namespace Yamldocs;

use Symfony\Component\Yaml\Yaml;

class YamldocsConfig
{
    public string $filePath;

    public array $parameters = [];

    public function load(string $filePath): void
    {
        $this->filePath = $filePath;
        $this->parameters = Yaml::parseFile($filePath);
        $this->loadEnvVars();
    }

    public function loadEnvVars(): void
    {
        foreach (getenv() as $name => $value) {
            if (str_starts_with($name,'YAMLDOCS_')) {
                $this->set($name, $value);
            }
        }
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
