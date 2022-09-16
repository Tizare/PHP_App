<?php

namespace PHP2\App\Response;

class SuccessfulResponse extends Response
{
    protected const SUCCESS =  true;
    private array $data;

    public function __construct($data = [])
    {
        $this->data = $data;
    }

    protected function payload(): array
    {
        return ['data' => $this->data];
    }
}