<?php

namespace App;

interface BuilderInterface
{
    public function configure(array $options = []): void;

    public function getMarkdown(Document $document): string;
}
