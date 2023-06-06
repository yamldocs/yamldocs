<?php

namespace App\Command\Build;

use Exception;
use Minicli\Command\CommandController;
use Minicli\FileNotFoundException;
use Yamldocs\BuilderService;
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

        if ($yamlFile === null) {
            throw new Exception("Missing 'file' parameter");
        }

        $document = new Document($yamlFile);
        if ($output === null) {
            $output = $this->getApp()->config->app_root . '/' . $document->getName() . '.md';
        }

        $builderName = "default";
        if ($this->hasParam('builder')) {
            $builderName = $this->getParam('builder');
        }

        /** @var BuilderService $builderService */
        $builderService = $this->getApp()->builder;
        $builder = $builderService->getBuilder($builderName);

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
