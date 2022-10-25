<?php

namespace App\Command\Build;

use App\Document;
use Minicli\Command\CommandController;
use Minicli\FileNotFoundException;

class MarkdownController extends CommandController
{
    /**
     * @throws FileNotFoundException
     * @throws \Exception
     */
    public function handle(): void
    {
        $yamlFile = $this->getParam('file');
        $output = $this->getParam('output');
        $baseDir = __DIR__ . "/../../../";

        if ($yamlFile === null) {
            $this->getPrinter()->error('You must provide a "file=" parameter pointing to the YAML file that you want to build docs from.');
            throw new \Exception("Missing 'file' parameter");
        }

        if ($output === null) {
            $output = $baseDir . '/var/output/' . basename($yamlFile) . '.md';
        }

        $document = new Document($yamlFile);
        $document->buildMarkdown();
        $document->save($output);

        $this->getPrinter()->success("Markdown build finished.");
    }
}
