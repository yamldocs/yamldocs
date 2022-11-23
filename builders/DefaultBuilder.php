<?php

namespace Builders;

use App\BuilderInterface;
use App\Mark;
use Minicli\FileNotFoundException;
use Minicli\Stencil;
use Symfony\Component\Yaml\Yaml;

class DefaultBuilder implements BuilderInterface
{
    public array $headers = [ 'Directive', 'Details'];
    public string $templateDir;

    public static string $TPL_PAGE = "reference_page";
    public static string $TPL_SECTION = "reference_page_section";

    public function __construct()
    {
        $this->setTemplateDir( __DIR__ . '/../templates');
    }

    public function configure(array $options = []): void
    {
        if ($options['templateDir']) {
            $this->setTemplateDir($options['templateDir']);
        }
    }

    /**
     * @param string $templateDir
     * @return void
     */
    public function setTemplateDir(string $templateDir)
    {
        $this->templateDir = $templateDir;
    }

    /**
     * @param string $title
     * @param string $description
     * @param array $nodes
     * @param array $meta
     * @return string
     * @throws FileNotFoundException
     */
    public function getMarkdown(string $title, string $description, array $nodes, array $meta = []): string
    {
        $stencil = new Stencil($this->templateDir);

        return $stencil->applyTemplate(self::$TPL_PAGE, [
            'title' => $title,
            'description' => $description,
            'content' => $this->buildSections($nodes, $meta)
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
