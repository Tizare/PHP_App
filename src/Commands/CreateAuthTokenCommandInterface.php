<?php

namespace PHP2\App\Commands;

use PHP2\App\user\AuthToken;

interface CreateAuthTokenCommandInterface
{
        public function handle(AuthToken $authToken): void;
}