<?php

namespace App;

interface BuilderInterface
{
    public function getMarkdown(string $title, string $description, array $nodes, array $meta = []): string;
}
