<?php

declare(strict_types=1);

namespace App\Habitica;

use App\Http\Http;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

final readonly class Habitica
{
    public function __construct(private Http $http)
    {
    }

    public static function create(string $baseUrl, string $apiKey, string $apiUser): self
    {
        $client = HttpClient::createForBaseUri($baseUrl, [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-Client' => '49de7a0b-cad8-4788-830b-8299c34e96a1 - clibitica',
                'X-Api-Key' => $apiKey,
                'X-Api-User' => $apiUser,
            ],
        ]);

        $serializer = new Serializer(
            normalizers: [
                new ArrayDenormalizer(),
                new DateTimeNormalizer(),
                new BackedEnumNormalizer(),
                new ObjectNormalizer(propertyTypeExtractor: new PhpDocExtractor()),
            ],
            encoders: [new JsonEncoder()],
        );

        return new self(new Http($client, $serializer));
    }

    public function createTask(Task\Create\Request $request): Task\Create\Response
    {
        return $this->http
            ->post('api/v3/tasks/user')
            ->status(201)
            ->bodyJson($request)
            ->fetchJson(Task\Create\Response::class)
        ;
    }

    public function deleteTask(string $id): void
    {
        $this->http
            ->delete('api/v3/tasks/{id}', ['id' => $id])
            ->fetch()
        ;
    }

    public function listTasks(): Task\List\Response
    {
        return $this->http
            ->get('api/v3/tasks/user')
            ->fetchJson(Task\List\Response::class)
        ;
    }

    public function scoreTask(string $id, Task\ScoreDirection $direction): void
    {
        $this->http
            ->post('api/v3/tasks/{id}/score/{direction}', ['id' => $id, 'direction' => $direction->value])
            ->fetch()
        ;
    }

    public function createTag(Tag\Create\Request $request): Tag\Create\Response
    {
        return $this->http
            ->post('api/v3/tags')
            ->status(201)
            ->bodyJson($request)
            ->fetchJson(Tag\Create\Response::class)
        ;
    }

    public function deleteTag(string $id): void
    {
        $this->http
            ->delete('api/v3/tags/{id}', ['id' => $id])
            ->fetch()
        ;
    }

    public function listTags(): Tag\List\Response
    {
        return $this->http
            ->get('api/v3/tags')
            ->fetchJson(Tag\List\Response::class)
        ;
    }
}
