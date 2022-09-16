<?php

namespace PHP2\App\Request;

use JsonException;
use PHP2\App\Exceptions\HttpException;

class Request
{
    private array $get;
    private string $post;
    private array $server;
    private array $cookies;

    public function __construct(array $get = [], array $server = [], string $post = '',  array $cookies = [])
    {
        $this->get = $get;
        $this->server = $server;
        $this->post = $post;
        $this->cookies = $cookies;
    }

    /**
     * @throws HttpException
     */
    public function path(): string
    {
        if (!array_key_exists('REQUEST_URI', $this->server)) {
            throw new HttpException("Cannot get path from the request");
        }

        $components = parse_url($this->server['REQUEST_URI']);
        if (!is_array($components) || !array_key_exists('path', $components)) {
            throw new HttpException('Cannot parse path from request');
        }
        return $components['path'];
    }

    /**
     * @throws HttpException
     */
    public function query(string $param): string
    {
        if (!array_key_exists($param, $this->get)) {
            throw new HttpException("No such query param in the request: $param");
        }

        $value = trim($this->get[$param]);

        if (empty($value)) {
            throw new HttpException("Empty query param in the request: $param");
        }

        return $value;
    }

    /**
     * @throws HttpException
     */
    public function header(string $header): string
    {
        $headerName = mb_strtoupper("http_" . str_replace('-', '_', $header));

        if (!array_key_exists($headerName, $this->server)) {
            throw new HttpException("No such header in the request: $header");
        }

        $value = trim($this->server[$headerName]);

        if (empty($value)) {
            throw new HttpException("Empty header in the request: $header");
        }

        return $value;
    }

    /**
     * @throws HttpException
     */
    public function jsonBody(): array
    {
        try {
            $data = json_decode(
                $this->post,
                $associative = true,
                $flags = JSON_THROW_ON_ERROR
            );
        } catch (JsonException $exception) {
            throw new HttpException("Cannot decode json body");
        }

        if (!is_array($data)) {
            throw new HttpException("Not an array in json body");
        }

        return $data;
    }

    /**
     * @throws HttpException
     */
    public function jsonBodyField(string $field)
    {
        $data = $this->jsonBody();

        if (!array_key_exists($field, $data)) {
            throw new HttpException("No such field - $field");
        }

        if (empty($data[$field])) {
            throw new HttpException("Empty field - $field");
        }

        return $data[$field];
    }

    /**
     * @throws HttpException
     */
    public function method(): string
    {
        if (!array_key_exists('REQUEST_METHOD', $this->server)) {
            throw new HttpException('Cannot get method from the request');
        }

        return $this->server['REQUEST_METHOD'];
    }

}