<?php

use Minicli\Config;
use Yamldocs\Builder\TestBuilder;
use Yamldocs\Builder\DefaultBuilder;

test('TestBuilder works', function () {
    $builder = new TestBuilder();
    $markdown = $builder->getMarkdown(getDocument());

    $this->assertEquals("test", $markdown);
});

test('DefaultBuilder loads YAML', function () {
    $builder = new DefaultBuilder();
    $builder->configure(new Config(getConfigAsArray()));
    $markdown = $builder->getMarkdown(getDocument());

    $this->assertStringContainsString("### Reference", $markdown);
});
