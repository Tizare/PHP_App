<?php

namespace PHP2\App\Response;

class ErrorResponse extends Response
{
    protected const SUCCESS = false;
    private string $reason;

    public function  __construct($reason = 'Something goes wrong')
    {
        $this->reason = $reason;
    }

    protected function payload(): array
    {
        return ['reason' => $this->reason];
    }
}