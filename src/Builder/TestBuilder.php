<?php

namespace Yamldocs\Builder;

use Minicli\Config;
use Yamldocs\BuilderInterface;
use Yamldocs\Document;

class TestBuilder implements BuilderInterface
{
    public function configure(Config $config, array $builderOptions = []): void
    {
        //
    }

    public function getMarkdown(Document $document): string
    {
        return "test";
    }
}
