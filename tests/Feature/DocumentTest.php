<?php

use App\Document;
use Builders\DefaultBuilder;
use Builders\TestBuilder;
use Symfony\Component\Yaml\Yaml;

test('Document is created and default builder is set', function () {
    $document = getDocument();

    $this->assertEquals("test.yaml", basename($document->filePath));
    $this->assertInstanceOf(DefaultBuilder::class, $document->builder);
});

test('Custom builder can be set', function () {
    $document = getDocument();
    $builder = new TestBuilder();
    $document->setBuilder($builder);
    $this->assertInstanceOf(TestBuilder::class, $document->builder);
});

test('Name is correctly set', function () {
    $document = getDocument();
    $this->assertEquals('test', $document->getName());
});

test('YAML is correctly loaded', function () {
    $document = getDocument();
    $this->assertIsArray($document->yaml);
    $this->assertArrayHasKey('contents', $document->yaml);
    $this->assertArrayHasKey('accounts', $document->yaml);
});

test('Markdown is generated', function () {
    $document = getDocument();
    $document->buildMarkdown();

    $this->assertStringContainsString("## contents", $document->markdown);
});
