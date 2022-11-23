<?php

namespace App;

interface BuilderInterface
{
    public function configure(array $options = []): void;

    public function getMarkdown(string $title, string $description, array $nodes, array $meta = []): string;
}
