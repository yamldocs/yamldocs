<?php

namespace App\Command\Build;

use App\Mark;
use Minicli\Command\CommandController;
use Minicli\Stencil;
use Symfony\Component\Yaml\Yaml;

class MarkdownController extends CommandController
{
    static string $TPL_PAGE = "reference_page";
    static string $TPL_SECTION = "reference_page_section";

    public function handle(): void
    {
        $file = $this->getParam('file');

        if ($file === null) {
            $this->getPrinter()->error('You must provide a "file=" parameter pointing to the YAML file that you want to build docs to.');
            throw new \Exception("Missing 'file' parameter");
        }

        $document = Yaml::parseFile($file);
        $meta = [];
        if (isset($document['_meta'])) {
            $meta = $document['_meta'];
            unset($document['_meta']);
        }

        $title = $meta['_title'] ?? basename($file);

        $stencil = new Stencil($this->getApp()->config->stencilDir);

        $content = $stencil->applyTemplate(self::$TPL_PAGE, [
            'title' => $title,
            'description' => "$title YAML reference",
            'content' => $this->buildSections($document, $meta)
        ]);

        $output = $this->getParam('output') ?: __DIR__ . "/../../../var/output/" . $title . ".md";
        try {
            $outputFile = fopen($output, "w+");
            fwrite($outputFile, $content);
            fclose($outputFile);

        } catch (\Exception $exception) {
            $this->getPrinter()->error("Error while trying to save the file.");
        }

        $this->getPrinter()->success("Markdown build finished.");
    }

    public function buildSections(array $nodes, array $meta = []): string
    {
        $content = "";
        $stencil = new Stencil($this->getApp()->config->stencilDir);

        foreach ($nodes as $key => $item) {
            $this->getPrinter()->info("Building node '$key'...");
            $referenceTable = "";
            $example = "";
            if (is_array($item)) {
                $refTable = $this->buildReferenceTable($item, $meta[$key] ?? []);
                $referenceTable = Mark::table($refTable, ['Directive', 'Expects', ' ']);
                $example = Yaml::dump($item);
            }
            $content .= "\n" . $stencil->applyTemplate(self::$TPL_SECTION, [
                    'item' => $key,
                    'description' => $meta[$key]['info'] ?? " ",
                    'reference_table' => $referenceTable,
                    'example' => $example,
                    'notes' => $meta[$key]['notes'] ?? " "
                ]);

        }
        return $content;
    }

    public function buildReferenceTable(array $content, array $meta = []): array
    {
        $table = [];
        foreach ($content as $item => $value) {
            $expects = is_array($value) ? "Array" : "String";
            $itemInfo = "";

            if (isset($meta['items'][$item])) {
                $itemInfo = (string) $meta['items'][$item];
            }

            $table[] = [ $item, $expects, $itemInfo ];
        }

        return $table;
    }
}