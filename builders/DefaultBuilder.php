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
    public string $tplSection;

    public function __construct(string $templateDir, string $tplSection)
    {
        $this->templateDir = $templateDir;
        $this->tplSection = $tplSection;
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
