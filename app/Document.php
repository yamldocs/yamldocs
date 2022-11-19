<?php

namespace App;

use Builders\DefaultBuilder;
use Minicli\FileNotFoundException;
use Minicli\Stencil;
use Symfony\Component\Yaml\Yaml;

class Document
{
    public string $filePath;
    public string $templateDir;
    public array $yaml = [];
    public array $meta = [];
    public string $markdown;
    static string $TPL_PAGE = "reference_page";
    static string $TPL_SECTION = "reference_page_section";
    public BuilderInterface $builder;

    /**
     * @param string $filePath
     * @param string|null $templateDir
     */
    public function __construct(string $filePath, string $templateDir = null)
    {
        $this->filePath = $filePath;
        $this->templateDir = $templateDir ?? __DIR__ . '/../templates';
        $this->builder = new DefaultBuilder($this->templateDir, self::$TPL_SECTION);
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

    public function getMeta($key)
    {
        return $this->meta[$key] ?? null;
    }

    /**
     * @return void
     * @throws FileNotFoundException
     */
    public function buildMarkdown(): void
    {
        $title = $this->getMeta('_title') ?? str_replace(".yaml", "", basename($this->filePath));
        $description = $this->getMeta('_description') ?? "$title reference";

        $stencil = new Stencil($this->templateDir);

        $this->markdown = $stencil->applyTemplate(self::$TPL_PAGE, [
            'title' => $title,
            'description' => $description,
            'content' => $this->builder->buildSections($this->yaml, $this->meta)
        ]);
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

    public function loadBuilders()
    {

    }
}