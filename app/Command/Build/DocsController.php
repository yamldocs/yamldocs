<?php

namespace App\Command\Build;

use App\Document;
use Minicli\Command\CommandController;
use Minicli\FileNotFoundException;

class DocsController extends CommandController
{
    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        $dir = $this->getParam('source');
        $output = $this->getParam('output');
        $builder = "default";

        if ($dir === null) {
            $this->getPrinter()->error('You must provide a "source=" parameter pointing to the directory containing yaml files to build docs from.');
            throw new \Exception("Missing 'source' parameter");
        }

        if ($output === null) {
            $this->getPrinter()->error('You must provide a "output=" parameter pointing to a directory where to output docs.');
            throw new \Exception("Missing 'output' parameter");
        }

        if (!is_dir($dir)) {
            $this->getPrinter()->error('Source directory not found.');
            throw new \Exception("Source directory $dir not found.");
        }

        if (!is_dir($output)) {
            $this->getPrinter()->error('Output directory not found.');
            throw new \Exception("Output $output not found.");
        }

        if ($this->hasParam('builder') && ($this->getParam('builder') !== 'default')) {
            $builder = $this->getParam('builder');
        }

        foreach (glob($dir . "/*.yaml") as $yamlFile) {
            $fileOut = $output . "/" . str_replace(".yaml", "", basename($yamlFile)) . '.md';
            $commandCall = ['yamldocs', 'build', 'markdown', "file=$yamlFile", "output=$fileOut", "builder=$builder"];

            if ($this->hasParam('tpl_dir')) {
                $commandCall[] = "tpl_dir=" . $this->getParam('tpl_dir');
            }

            if ($this->hasParam('node')) {
                $commandCall[] = "node=" . $this->getParam('node');
            }

            $this->getApp()->runCommand($commandCall);
        }

        $this->getPrinter()->success("Docs markdown build finished.");
    }
}
