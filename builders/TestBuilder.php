<?php

namespace Builders;

use App\BuilderInterface;

class TestBuilder implements BuilderInterface
{
    public function buildSections(array $nodes): string
    {
        return "test";
    }
}
