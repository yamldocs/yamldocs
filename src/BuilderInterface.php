<?php

namespace Yamldocs;

use Minicli\Config;

interface BuilderInterface
{
    public function configure(Config $config): void;

    public function getMarkdown(Document $document): string;
}
