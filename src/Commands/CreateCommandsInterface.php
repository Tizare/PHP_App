<?php

namespace PHP2\App\Commands;

use PHP2\App\Argument\Argument;

interface CreateCommandsInterface
{
    public function handle(Argument $argument): void;
}