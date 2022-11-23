<?php

namespace App;

use Builders\DefaultBuilder;
use Minicli\FileNotFoundException;
use Symfony\Component\Yaml\Yaml;

class Document
{
    public string $filePath;
    public string $templateDir;
    public array $yaml = [];
    public array $meta = [];
    public string $markdown;
    public BuilderInterface $builder;

    /**
     * @param string $filePath
     * @param string|null $templateDir
     */
    public function __construct(string $filePath, string $templateDir = null)
    {
        $this->filePath = $filePath;
        $this->templateDir = $templateDir ?? __DIR__ . '/../templates';
        $this->builder = new DefaultBuilder();
        $this->builder->setTemplateDir($this->templateDir);
        $this->loadYaml();
    }

    public function setBuilder(BuilderInterface $builder): void
    {
        $this->builder = $builder;
    }

    /**
     * @return void
     */
    public function loadYaml(): void
    {
        $document = Yaml::parseFile($this->filePath);
        if (isset($document['_meta'])) {
            $this->meta = $document['_meta'];
            unset($document['_meta']);
        }
        $this->yaml = $document;
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
     * @return void
     * @throws FileNotFoundException
     */
    public function buildMarkdown(): void
    {
        $title = $this->getName();
        $description = $this->getMeta('_description') ?? "$title reference";

        $this->markdown = $this->builder->getMarkdown($title, $description, $this->yaml, $this->meta);
    }

    /**
     * @param string $filePath
     * @return void
     */
    public function save(string $filePath): void
    {
        $outputFile = fopen($filePath, "w+");
        fwrite($outputFile, $this->markdown);
        fclose($outputFile);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->getMeta('_title') ?? str_replace(".yaml", "", basename($this->filePath));
    }
}
