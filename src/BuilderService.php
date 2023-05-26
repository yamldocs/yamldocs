<?php

namespace Yamldocs;

use Minicli\App;
use Minicli\ServiceInterface;

class BuilderService implements ServiceInterface
{
    public array $builders;

    public function load(App $app): void
    {
        $builders = $app->config->builders;
        if (!is_array($builders)) {
            throw new \Exception("'builders' config not found.");
        }

        foreach ($builders as $name => $builderClass) {
            $builder = new $builderClass;
            $builder->configure($app->config);
            $this->registerBuilder($name, $builder);
        }
    }

    public function registerBuilder(string $name, BuilderInterface $builder)
    {
        $this->builders[$name] = $builder;
    }

    public function getBuilder(string $builderName)
    {
        if (!isset($this->builders[$builderName])) {
            throw new \Exception("Builder $builderName not found.");
        }

        return $this->builders[$builderName];
    }
}