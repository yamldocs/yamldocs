<?php

namespace App;

interface BuilderInterface
{
    public function buildSections(array $nodes): string;
}
