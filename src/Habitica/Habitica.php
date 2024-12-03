<?php

declare(strict_types=1);

namespace App\Habitica;

use RuntimeException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

use function sprintf;

use const PHP_EOL;

final readonly class Habitica
{
    public function __construct(private HttpClientInterface $client, private SerializerInterface $serializer)
    {
    }

    public function createTask(Task\Create\Request $request): Task\Create\Response
    {
        $response = $this->do('POST', 'api/v3/tasks/user', 201, [
            'body' => $this->serialize($request),
        ]);

        return $this->deserialize($response, Task\Create\Response::class);
    }

    public function deleteTask(string $id): void
    {
        $this->do('DELETE', sprintf('api/v3/tasks/%s', rawurlencode($id)), 200);
    }

    public function listTasks(): Task\List\Response
    {
        $response = $this->do('GET', 'api/v3/tasks/user', 200);

        return $this->deserialize($response, Task\List\Response::class);
    }

    public function scoreTask(string $id, Task\ScoreDirection $direction): void
    {
        $this->do('POST', sprintf('api/v3/tasks/%s/score/%s', rawurlencode($id), $direction->value), 200);
    }

    public function createTag(Tag\Create\Request $request): Tag\Create\Response
    {
        $response = $this->do('POST', 'api/v3/tags', 201, [
            'body' => $this->serialize($request),
        ]);

        return $this->deserialize($response, Tag\Create\Response::class);
    }

    public function deleteTag(string $id): void
    {
        $this->do('DELETE', sprintf('api/v3/tags/%s', rawurlencode($id)), 200);
    }

    public function listTags(): Tag\List\Response
    {
        $response = $this->do('GET', 'api/v3/tags', 200);

        return $this->deserialize($response, Tag\List\Response::class);
    }

    /**
     * @param array<string, mixed> $options
     */
    private function do(string $method, string $uri, int $status, array $options = []): ResponseInterface
    {
        $response = $this->client->request($method, $uri, $options);
        if ($response->getStatusCode() !== $status) {
            /** @var string $url */
            $url = $response->getInfo('url');

            $message = [];
            $message[] = sprintf(
                '%d return for %s %s, expected %d',
                $response->getStatusCode(),
                $method,
                $url,
                $status,
            );
            $message[] = sprintf('response body: %s', $response->getContent(false));

            throw new RuntimeException(implode(PHP_EOL, $message));
        }

        return $response;
    }

    private function serialize(mixed $data): string
    {
        return $this->serializer->serialize($data, 'json');
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $type
     *
     * @return T
     */
    private function deserialize(ResponseInterface $response, string $type): mixed
    {
        return $this->serializer->deserialize($response->getContent(), $type, 'json');
    }
}
