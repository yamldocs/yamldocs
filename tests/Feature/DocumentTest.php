<?php

test('Document is created', function () {
    $document = getDocument();

    $this->assertEquals("test.yaml", basename($document->filePath));
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

test('Metadata is loaded', function ()  {
    $document = getDocument();
    $this->assertEquals("My test title", $document->title);
});