<?php

namespace App\Command\Build;

use Minicli\Command\CommandController;
use Minicli\Exception\CommandNotFoundException;
use Throwable;

class DocsController extends CommandController
{
    /**
     * @throws CommandNotFoundException
     * @throws Throwable
     */
    public function handle(): void
    {
        $dir = $this->getParam('source');
        $output = $this->getParam('output');
        $builder = "default";

        if ($dir === null) {
            $this->error('You must provide a "source=" parameter pointing to the directory containing yaml files to build docs from.');
            throw new \Exception("Missing 'source' parameter");
        }

        if ($output === null) {
            $this->error('You must provide a "output=" parameter pointing to a directory where to output docs.');
            throw new \Exception("Missing 'output' parameter");
        }

        if (!is_dir($dir)) {
            $this->error('Source directory not found.');
            throw new \Exception("Source directory $dir not found.");
        }

        if (!is_dir($output)) {
            $this->error('Output directory not found.');
            throw new \Exception("Output $output not found.");
        }

        if ($this->hasParam('builder') && ($this->getParam('builder') !== 'default')) {
            $builder = $this->getParam('builder');
        }

        foreach (glob($dir . "/*") as $input) {
            if (is_dir($input) && !$this->hasFlag('recursive')) {
                continue;
            }

            $this->buildDocs($input, $output, $builder);
        }

        $this->success("Docs markdown build finished.");
    }

    /**
     * @param string $input
     * @param string $output
     * @param string $builder
     * @return void
     * @throws CommandNotFoundException
     */
    public function buildDocs(string $input, string $output, string $builder): void
    {
        if (is_dir($input)) {
            if (basename($input) === "_meta") {
                return;
            }

            foreach (glob($input . "/*") as $path) {
                $outputDir = $output . "/" . basename(dirname($path));
                $this->buildDocs($path, $outputDir, $builder);
            }
            return;
        }

        if (!is_dir($output)) {
            mkdir($output, 0777, true);
        }
        $fileOut = $output . "/" . $this->getOutputPath($input);
        $commandCall = $this->getCommandCall($input, $fileOut, $builder);

        if ($this->hasParam('tpl_dir')) {
            $commandCall[] = "tpl_dir=" . $this->getParam('tpl_dir');
        }

        if ($this->hasParam('node')) {
            $commandCall[] = "node=" . $this->getParam('node');
        }

        try {
            $this->getApp()->runCommand($commandCall);
        } catch (InputError $exception) {
            $this->getPrinter()->out("Invalid YAML found: $input. Skipping...");
        }

    }

    /**
     * @param string $sourcePath
     * @return string
     */
    public function getOutputPath(string $sourcePath): string
    {
        return str_replace(".yaml", "", basename($sourcePath)) . '.md';
    }

    /**
     * @param string $input
     * @param string $output
     * @param string $builder
     * @return string[]
     */
    public function getCommandCall(string $input, string $output, string $builder): array
    {
        return ['yamldocs', 'build', 'markdown', "file=$input", "output=$output", "builder=$builder"];
    }
}
