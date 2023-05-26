<?php

namespace App\Command\Build;

use Minicli\App;
use Minicli\Command\CommandController;

class DefaultController extends CommandController
{
    public function handle(): void
    {
        $this->info('Run ./yamldocs build markdown');
    }
}
