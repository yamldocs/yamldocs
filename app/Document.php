<?php

namespace App;

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
        $title = $this->getMeta('_title') ?? basename($this->filePath);
        $description = $this->getMeta('_description') ?? "$title YAML reference";

        $stencil = new Stencil($this->templateDir);

        $this->markdown = $stencil->applyTemplate(self::$TPL_PAGE, [
            'title' => $title,
            'description' => $description,
            'content' => $this->buildSections($this->yaml, $this->meta)
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

    /**
     * @param array $nodes
     * @return string
     * @throws FileNotFoundException
     */
    public function buildSections(array $nodes): string
    {
        $content = "";

        foreach ($nodes as $key => $item) {
            $referenceTable = "";
            $example = "";

            if (is_array($item)) {
                $refTable = $this->buildReferenceTable($item, $this->getMeta($key) ?? []);
                $referenceTable = Mark::table($refTable, ['Directive', 'Expects']);
                $example = Yaml::dump([$key => $item], 6, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);
            }

            $content .= "\n" . $this->buildSectionContent(
                $key,
                $this->getMeta($key)['info'] ?? " ",
                $referenceTable,
                $example,
                $this->getMeta($key)['notes'] ?? " "
            );
        }
        return $content;
    }

    /**
     * @param string $item
     * @param string $description
     * @param string $referenceTable
     * @param string $example
     * @param string $notes
     * @return string
     * @throws FileNotFoundException
     */
    public function buildSectionContent(string $item, string $description, string $referenceTable, string $example, string $notes): string
    {
        $stencil = new Stencil($this->templateDir);
        return $stencil->applyTemplate(self::$TPL_SECTION, [
                'item' => $item,
                'description' => $description,
                'reference_table' => $referenceTable,
                'example' => $example,
                'notes' => $notes
            ]);
    }

    /**
     * @param array $content
     * @param array $meta
     * @return array
     */
    public function buildReferenceTable(array $content, array $meta = []): array
    {
        $table = [];
        $listed = [];
        foreach ($content as $item => $value) {
            if (is_numeric($item)) {
                //this is a numeric array; find nested unique keys
                foreach ($value as $key => $exampleValue) {
                    if (!in_array($key, $listed)) {
                        $table[] = $this->renderTableRow($key, $exampleValue, $meta);
                        $listed[] = $key;
                    }
                }
                continue;
            }
            $table[] = $this->renderTableRow($item, $value, $meta);
        }

        return $table;
    }

    public function renderTableRow(string $directive, mixed $details, array $meta = []): array
    {
        $valueType = is_array($details) ? "Array" : "String";
        $details = "($valueType) ";

        if (isset($meta['items'][$directive])) {
            $details .=  (string) $meta['items'][$directive];
        }

        return [ $directive, $details ];
    }
}