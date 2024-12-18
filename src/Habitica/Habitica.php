<?php

declare(strict_types=1);

namespace App\Habitica;

use App\Http\Http;
use DateInterval;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\RateLimiter\Policy\FixedWindowLimiter;
use Symfony\Component\RateLimiter\Storage\CacheStorage;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorFromClassMetadata;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\Cache\CacheInterface;

final readonly class Habitica
{
    private const string CACHE_TASKS = 'tasks';
    private const string CACHE_TAGS = 'tags';

    public function __construct(private Http $http, private CacheInterface $cache)
    {
    }

    public static function create(string $baseUrl, string $apiKey, string $apiUser, ?string $cacheDir = null): self
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

        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());

        $serializer = new Serializer(
            normalizers: [
                new Task\DifficultyNormalizer(),
                new ArrayDenormalizer(),
                new DateTimeNormalizer(),
                new BackedEnumNormalizer(),
                new ObjectNormalizer(
                    classMetadataFactory: $classMetadataFactory,
                    nameConverter: new MetadataAwareNameConverter($classMetadataFactory),
                    propertyTypeExtractor: new PhpDocExtractor(),
                    classDiscriminatorResolver: new ClassDiscriminatorFromClassMetadata($classMetadataFactory),
                ),
            ],
            encoders: [new JsonEncoder()],
        );

        $lockFactory = new LockFactory(new FlockStore($cacheDir.'/lock'));

        $rateLimiter = new FixedWindowLimiter(
            id: 'api',
            limit: 30,
            interval: new DateInterval('PT1M'),
            storage: new CacheStorage(new FilesystemAdapter(directory: $cacheDir.'/rate-limiter-storage')),
            lock: $lockFactory->createLock('api'),
        );

        return new self(
            http: new Http($client, $serializer, $rateLimiter),
            cache: new FilesystemAdapter(directory: $cacheDir.'/cache'),
        );
    }

    public function createTask(Task\Create\Request $request): Task\Create\Response
    {
        try {
            return $this->http
                ->post('api/v3/tasks/user')
                ->status(201)
                ->bodyJson($request)
                ->fetchJson(Task\Create\Response::class)
            ;
        } finally {
            $this->cache->delete(self::CACHE_TASKS);
        }
    }

    public function deleteTask(string $id): void
    {
        try {
            $this->http
                ->delete('api/v3/tasks/{id}', ['id' => $id])
                ->fetch()
            ;
        } finally {
            $this->cache->delete(self::CACHE_TASKS);
        }
    }

    public function listTasks(): Task\List\Response
    {
        return $this->cache->get(self::CACHE_TASKS, function (CacheItemInterface $item): Task\List\Response {
            $item->expiresAfter(60 * 60);

            return $this->http
                ->get('api/v3/tasks/user')
                ->fetchJson(Task\List\Response::class)
            ;
        });
    }

    public function task(string $id): Task\Get\Response
    {
        return $this->http
            ->get('api/v3/tasks/{id}', ['id' => $id])
            ->fetchJson(Task\Get\Response::class)
        ;
    }

    public function scoreTask(string $id, Task\ScoreDirection $direction): void
    {
        try {
            $this->http
                ->post('api/v3/tasks/{id}/score/{direction}', ['id' => $id, 'direction' => $direction->value])
                ->fetch()
            ;
        } finally {
            $this->cache->delete(self::CACHE_TASKS);
        }
    }

    public function addTagToTask(string $taskId, string $tagId): void
    {
        try {
            $this->http
                ->post('api/v3/tasks/{taskId}/tags/{tagId}', ['taskId' => $taskId, 'tagId' => $tagId])
                ->fetch()
            ;
        } finally {
            $this->cache->delete(self::CACHE_TASKS);
        }
    }

    public function deleteTagFromTask(string $taskId, string $tagId): void
    {
        try {
            $this->http
                ->delete('api/v3/tasks/{taskId}/tags/{tagId}', ['taskId' => $taskId, 'tagId' => $tagId])
                ->fetch()
            ;
        } finally {
            $this->cache->delete(self::CACHE_TASKS);
        }
    }

    public function createTag(Tag\Create\Request $request): Tag\Create\Response
    {
        try {
            return $this->http
                ->post('api/v3/tags')
                ->status(201)
                ->bodyJson($request)
                ->fetchJson(Tag\Create\Response::class)
            ;
        } finally {
            $this->cache->delete(self::CACHE_TAGS);
        }
    }

    public function deleteTag(string $id): void
    {
        try {
            $this->http
                ->delete('api/v3/tags/{id}', ['id' => $id])
                ->fetch()
            ;
        } finally {
            $this->cache->delete(self::CACHE_TAGS);
            $this->cache->delete(self::CACHE_TASKS);
        }
    }

    public function listTags(): Tag\List\Response
    {
        return $this->cache->get(self::CACHE_TAGS, function (CacheItemInterface $item): Tag\List\Response {
            $item->expiresAfter(1 * 60 * 60);

            return $this->http
                ->get('api/v3/tags')
                ->fetchJson(Tag\List\Response::class)
            ;
        });
    }

    public function getUser(): User\Get\Response
    {
        return $this->http
            ->get('api/v3/user')
            ->fetchJson(User\Get\Response::class)
        ;
    }
}
