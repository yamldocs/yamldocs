<?php

namespace Yamldocs;

use App\Exception\InputError;
use Symfony\Component\Yaml\Yaml;

class Document
{
    public string $filePath;
    public string $title;
    public array $yaml = [];
    public array $meta = [];
    public string $markdown;

    /**
     * @param string $filePath
<<<<<<< HEAD:src/Document.php
=======
     * @param string|null $templateDir
     * @throws \Exception
>>>>>>> main:app/Document.php
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->loadYaml();
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function loadYaml(): void
    {
        $content = Yaml::parseFile($this->filePath);
        if (!is_array($content)) {
            throw new InputError("This file could not be loaded. Are you sure it's YAML?");
        }

        $this->yaml = $content;
        $this->loadMetadata();
        $this->title = $this->getTitle();
    }

    /**
     * @return void
     */
    public function loadMetadata(): void
    {
        $meta = [];
        $metaFile = dirname($this->filePath) . '/_meta/' . basename($this->filePath);
        if (is_file($metaFile)) {
            $meta = Yaml::parseFile($metaFile);
        }

        if (isset($this->yaml['_meta'])) {
            $meta = array_merge($meta, $this->yaml['_meta']);
            unset($this->yaml['_meta']);
        }

        $this->meta = $meta;
    }

    /**
     * @param $key
     * @return string|null
     */
    public function getMeta($key): ?string
    {
        return $this->meta[$key] ?? null;
    }

    /**
     * @param string $filePath
     * @return void
     */
    public function saveMarkdown(string $filePath): void
    {
        $outputFile = fopen($filePath, "w+");
        fwrite($outputFile, $this->markdown);
        fclose($outputFile);
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getMeta('title') ?? $this->getName();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return str_replace(".yaml", "", basename($this->filePath));
    }
}
