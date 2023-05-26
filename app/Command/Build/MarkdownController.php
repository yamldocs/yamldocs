<?php

namespace App\Command\Build;

use Exception;
use Minicli\Command\CommandController;
use Minicli\FileNotFoundException;
use Yamldocs\Builder\DefaultBuilder;
use Yamldocs\Document;

class MarkdownController extends CommandController
{
    /**
     * @throws FileNotFoundException
     * @throws Exception
     */
    public function handle(): void
    {
        $yamlFile = $this->getParam('file');
        $output = $this->getParam('output');
        $baseDir = __DIR__ . "/../../../";
        $templatesDir = $this->getApp()->config->templatesDir;

        if ($yamlFile === null) {
            $this->error('You must provide a "file=" parameter pointing to the YAML file that you want to build docs from.');
            throw new Exception("Missing 'file' parameter");
        }

        if ($this->hasParam('tpl_dir')) {
            $templatesDir = $this->getParam('tpl_dir');
        }

        $document = new Document($yamlFile, $templatesDir);
        $builder = new DefaultBuilder();

        if ($output === null) {
            $output = $baseDir . '/' . $document->getName() . '.md';
        }

        if ($this->hasParam('builder') && ($this->getParam('builder') !== 'default')) {
            if (!$this->getApp()->config->has('builders')) {
                throw new Exception('Missing "builders" configuration.');
            }

            $requestedBuilder = $this->getParam('builder');
            $builders = $this->getApp()->config->builders;
            if (!isset($builders[$requestedBuilder])) {
                throw new Exception("Configuration not found for builder $requestedBuilder");
            }

            $class = $builders[$requestedBuilder];
            $builder = new $class();
        }

        $builder->configure($this->getApp()->config);

        if ($this->hasParam('node')) {
            $node = explode('.', $this->getParam('node'));
            $section = $document->yaml;
            foreach ($node as $item) {
                if (!key_exists($item, $section)) {
                    break;
                }
                $section = $section[$item];
            }
            $document->yaml = $section;
        }
        $document->markdown = $builder->getMarkdown($document);
        $document->saveMarkdown($output);

        $this->success("Finished building $output.");
    }
}
