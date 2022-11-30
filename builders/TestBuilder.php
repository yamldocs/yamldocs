<?php

namespace Builders;

use App\BuilderInterface;
use App\Document;

class TestBuilder implements BuilderInterface
{
    public function configure(array $options = []): void
    {
        //
    }

    public function getMarkdown(Document $document): string
    {
        return "test";
    }
}
