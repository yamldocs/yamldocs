<?php

use Builders\TestBuilder;
use Builders\DefaultBuilder;

test ('TestBuilder works', function () {
    $builder = new TestBuilder();
    $markdown = $builder->getMarkdown(getDocument());

    $this->assertEquals("test", $markdown);
});

test ('DefaultBuilder loads YAML', function () {
    $builder = new DefaultBuilder();
    $markdown = $builder->getMarkdown(getDocument());

    $this->assertStringContainsString("### Reference", $markdown);
});