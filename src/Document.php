<?php

namespace Yamldocs;

use Symfony\Component\Yaml\Yaml;

class Document
{
    public string $filePath;
    public string $templateDir;
    public string $title;
    public array $yaml = [];
    public array $meta = [];
    public string $markdown;

    /**
     * @param string $filePath
     * @param string|null $templateDir
     */
    public function __construct(string $filePath, string $templateDir = null)
    {
        $this->filePath = $filePath;
        $this->templateDir = $templateDir ?? __DIR__ . '/../templates';
        $this->loadYaml();
    }

    /**
     * @return void
     */
    public function loadYaml(): void
    {
        $this->yaml = Yaml::parseFile($this->filePath);
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
