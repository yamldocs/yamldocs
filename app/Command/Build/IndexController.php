<?php

namespace App\Command\Build;

use Exception;
use Minicli\Command\CommandController;
use Yamldocs\Mark;

class IndexController extends CommandController
{
    /**
     * @throws Exception
     */
    public function handle(): void
    {
        $dir = $this->getParam('source');
        $output = $this->getParam('output');

        if ($dir === null) {
            $this->error('You must provide a "source=" parameter pointing to the directory containing markdown files.');
            throw new Exception("Missing 'source' parameter");
        }

        if ($output === null) {
            $this->error('You must provide a "output=" parameter specifying a file path for saving your index.');
            throw new Exception("Missing 'output' parameter");
        }

        if (!is_dir($dir)) {
            $this->error('Source directory not found.');
            throw new Exception("Source directory $dir not found.");
        }

        $table = [];
        $columns = 4;
        foreach (glob($dir . "/*.md") as $markdown) {
            $row[] = sprintf("[%s](%s)", pathinfo($markdown)['filename'], $markdown);
            if (count($row) % $columns == 0) {
                $table[] = $row;
                $row = [];
            }
        }

        $content = Mark::table($table, array_fill(0, $columns, " "));

        $outputFile = fopen($output, "w+");
        fwrite($outputFile, $content);
        fclose($outputFile);

        $this->success("Docs index build finished.");
    }
}
