<?php

declare(strict_types=1);

namespace App\Http;

use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class Http
{
    private string $method;
    private string $path;
    private ?string $body = null;
    private int $status = 200;

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly SerializerInterface $serializer,
        private readonly ?LimiterInterface $limiter = null,
    ) {
    }

    /**
     * @param array<string, string> $params
     */
    public function get(string $path, array $params = []): self
    {
        return $this->endpoint('GET', $path, $params);
    }

    /**
     * @param array<string, string> $params
     */
    public function post(string $path, array $params = []): self
    {
        return $this->endpoint('POST', $path, $params);
    }

    /**
     * @param array<string, string> $params
     */
    public function put(string $path, array $params = []): self
    {
        return $this->endpoint('PUT', $path, $params);
    }

    /**
     * @param array<string, string> $params
     */
    public function delete(string $path, array $params = []): self
    {
        return $this->endpoint('DELETE', $path, $params);
    }

    /**
     * @param array<string, string> $params
     */
    private function endpoint(string $method, string $path, array $params): self
    {
        $preparedParams = [];
        foreach ($params as $key => $value) {
            $preparedParams['{'.$key.'}'] = rawurlencode($value);
        }

        $copy = clone $this;
        $copy->method = $method;
        $copy->path = strtr($path, $preparedParams);

        return $copy;
    }

    public function status(int $status): self
    {
        $copy = clone $this;
        $copy->status = $status;

        return $copy;
    }

    public function body(string $body): self
    {
        $copy = clone $this;
        $copy->body = $body;

        return $copy;
    }

    public function bodyJson(mixed $json): self
    {
        $copy = clone $this;
        $copy->body = $this->serializer->serialize($json, 'json');

        return $copy;
    }

    /**
     * @template T
     *
     * @param class-string<T> $type
     *
     * @return T
     */
    public function fetchJson(string $type): mixed
    {
        $response = $this->fetch();
        $content = $response->getContent(false);

        return $this->serializer->deserialize($content, $type, 'json');
    }

    public function fetch(): ResponseInterface
    {
        $options = [];
        if (null !== $this->body) {
            $options['body'] = $this->body;
        }

        $this->limiter?->reserve()->wait();

        $response = $this->client->request($this->method, $this->path, $options);
        if ($response->getStatusCode() !== $this->status) {
            throw new StatusCodeException($this->status, $response);
        }

        return $response;
    }
}
