<?php

namespace Yamldocs;

use Minicli\Config;

interface BuilderInterface
{
    public function configure(Config $config, array $builderOptions = []): void;

    public function getMarkdown(Document $document): string;
}
