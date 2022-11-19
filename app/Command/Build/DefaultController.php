<?php

namespace App\Command\Build;

use Minicli\App;
use Minicli\Command\CommandController;

class DefaultController extends CommandController
{
    public function handle(): void
    {
        $this->getPrinter()->info('Run ./yamldocs build markdown');
    }
}
