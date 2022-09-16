<?php

namespace PHP2\App\Handler;

use PHP2\App\Request\Request;
use PHP2\App\Response\Response;

interface HandlerInterface
{
    public function handle(Request $request): Response;
}