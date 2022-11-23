<?php

namespace Builders;

use App\BuilderInterface;

class TestBuilder implements BuilderInterface
{
    public function getMarkdown(string $title, string $description, array $nodes, array $meta = []): string
    {
        return "test";
    }
}
