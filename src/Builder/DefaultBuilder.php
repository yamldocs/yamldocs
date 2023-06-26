<?php

namespace Yamldocs\Builder;

use Minicli\Config;
use Minicli\FileNotFoundException;
use Minicli\Stencil;
use Symfony\Component\Yaml\Yaml;
use Yamldocs\BuilderInterface;
use Yamldocs\Document;
use Yamldocs\Mark;

class DefaultBuilder implements BuilderInterface
{
    public array $headers = ['Directive', 'Details'];
    public string $templatesDir;
    public array $builderOptions;
    public string $tplPage;
    public string $tplSection;

    /**
     * @throws FileNotFoundException
     */
    public function configure(Config $config, array $builderOptions = []): void
    {
        $this->builderOptions = $builderOptions;
        $templatesDir = envconfig('YAMLDOCS_TEMPLATES', $this->builderOptions['templatesDir'] ?? $config->templatesDir);
        $this->setTemplatesDir($templatesDir, $config);

        $this->tplPage = $this->builderOptions['tplPage'] ?? "reference_page";
        $this->tplSection = $this->builderOptions['tplSection'] ?? "reference_page_section";
    }

    /**
     * @param string $templatesDir
     * @param Config $config
     * @return void
     * @throws FileNotFoundException
     */
    public function setTemplatesDir(string $templatesDir, Config $config): void
    {
        if (!is_dir($templatesDir)) {
            if (!is_dir($config->app_root . '/' . $templatesDir)) {
                throw new FileNotFoundException("Templates directory not found.");
            }
            $templatesDir = $config->app_root . '/' . $templatesDir;
        }
        $this->templatesDir = $templatesDir;
    }

    /**
     * @param Document $document
     * @return string
     * @throws FileNotFoundException
     */
    public function getMarkdown(Document $document): string
    {
        $stencil = new Stencil($this->templatesDir);

        return $stencil->applyTemplate($this->tplPage, [
            'title' => $document->title,
            'description' => $document->getMeta('description'),
            'content' => $this->buildSections($document->yaml, $document->meta)
        ]);
    }

    /**
     * @throws FileNotFoundException
     */
    public function buildSections(array $nodes, array $meta = []): string
    {
        $content = "";

        foreach ($nodes as $key => $item) {
            $referenceTable = "";
            $example = "";

            if (is_array($item)) {
                $refTable = $this->buildReferenceTable($item, $meta[$key] ?? []);
                $referenceTable = Mark::table($refTable, $this->headers);
                $example = $meta[$key]['example'] ?? Yaml::dump([$key => $item], 6, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);
            }

            $content .= "\n" . $this->buildSectionContent(
                $key,
                $meta[$key]['info'] ?? " ",
                $referenceTable,
                $example,
                $meta[$key]['notes'] ?? " "
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
        $stencil = new Stencil($this->templatesDir);
        return $stencil->applyTemplate($this->tplSection, [
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
                if (!is_array($value)) {
                    $table[] = $this->renderTableRow($value, "", $meta);
                    continue;
                }
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

    /**
     * @param string $directive
     * @param mixed $details
     * @param array $meta
     * @return string[]
     */
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
