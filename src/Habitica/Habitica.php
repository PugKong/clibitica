<?php

declare(strict_types=1);

namespace App\Habitica;

use DateInterval;
use Psr\Cache\CacheItemInterface;
use Pugkong\Symfony\Requests\Request;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\ThrottlingHttpClient;
use Symfony\Component\HttpClient\UriTemplateHttpClient;
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

    public function __construct(private Request $request, private CacheInterface $cache)
    {
    }

    public static function create(string $baseUrl, string $apiKey, string $apiUser, ?string $cacheDir = null): self
    {
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

        $http = new ThrottlingHttpClient(new UriTemplateHttpClient(HttpClient::create()), $rateLimiter);

        $request = Request::create($http, $serializer)
            ->base($baseUrl)
            ->headers([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-Client' => '49de7a0b-cad8-4788-830b-8299c34e96a1 - clibitica',
                'X-Api-Key' => $apiKey,
                'X-Api-User' => $apiUser,
            ])
        ;

        return new self($request, new FilesystemAdapter(directory: $cacheDir.'/cache'));
    }

    public function createTask(Task\Create\Request $request): Task\Create\Response
    {
        try {
            return $this->request
                ->post('api/v3/tasks/user')
                ->body($request)
                ->response()
                ->checkStatus(201)
                ->object(Task\Create\Response::class)
            ;
        } finally {
            $this->cache->delete(self::CACHE_TASKS);
        }
    }

    public function deleteTask(string $id): void
    {
        try {
            $this->request
                ->delete('api/v3/tasks/{id}')
                ->var('id', $id)
                ->response()
                ->checkStatus(200)
            ;
        } finally {
            $this->cache->delete(self::CACHE_TASKS);
        }
    }

    public function updateTask(Task\Update\Request $request): Task\Update\Response
    {
        try {
            return $this->request
                ->put('/api/v3/tasks/{id}')
                ->var('id', $request->id)
                ->body($request)
                ->response()
                ->checkStatus(200)
                ->object(Task\Update\Response::class)
            ;
        } finally {
            $this->cache->delete(self::CACHE_TASKS);
        }
    }

    public function listTasks(): Task\List\Response
    {
        return $this->cache->get(self::CACHE_TASKS, function (CacheItemInterface $item): Task\List\Response {
            $item->expiresAfter(60 * 60);

            return $this->request
                ->get('api/v3/tasks/user')
                ->response()
                ->checkStatus(200)
                ->object(Task\List\Response::class)
            ;
        });
    }

    public function task(string $id): Task\Get\Response
    {
        return $this->request
            ->get('api/v3/tasks/{id}')
            ->var('id', $id)
            ->response()
            ->checkStatus(200)
            ->object(Task\Get\Response::class)
        ;
    }

    public function scoreTask(string $id, Task\ScoreDirection $direction): void
    {
        try {
            $this->request
                ->post('api/v3/tasks/{id}/score/{direction}')
                ->var('id', $id)
                ->var('direction', $direction->value)
                ->response()
                ->checkStatus(200)
            ;
        } finally {
            $this->cache->delete(self::CACHE_TASKS);
        }
    }

    public function addTagToTask(string $taskId, string $tagId): void
    {
        try {
            $this->request
                ->post('api/v3/tasks/{taskId}/tags/{tagId}')
                ->var('taskId', $taskId)
                ->var('tagId', $tagId)
                ->response()
                ->checkStatus(200)
            ;
        } finally {
            $this->cache->delete(self::CACHE_TASKS);
        }
    }

    public function deleteTagFromTask(string $taskId, string $tagId): void
    {
        try {
            $this->request
                ->delete('api/v3/tasks/{taskId}/tags/{tagId}')
                ->var('taskId', $taskId)
                ->var('tagId', $tagId)
                ->response()
                ->checkStatus(200)
            ;
        } finally {
            $this->cache->delete(self::CACHE_TASKS);
        }
    }

    public function addChecklistItem(Task\Checklist\Add\Request $request): Task\Checklist\Add\Response
    {
        try {
            return $this->request
                ->post('api/v3/tasks/{id}/checklist')
                ->var('id', $request->task)
                ->body($request)
                ->response()
                ->checkStatus(200)
                ->object(Task\Checklist\Add\Response::class)
            ;
        } finally {
            $this->cache->delete(self::CACHE_TASKS);
        }
    }

    public function deleteChecklistItem(Task\Checklist\Delete\Request $request): Task\Checklist\Delete\Response
    {
        try {
            return $this->request
                ->delete('api/v3/tasks/{task}/checklist/{item}')
                ->var('task', $request->task)
                ->var('item', $request->item)
                ->body($request)
                ->response()
                ->checkStatus(200)
                ->object(Task\Checklist\Delete\Response::class)
            ;
        } finally {
            $this->cache->delete(self::CACHE_TASKS);
        }
    }

    public function updateChecklistItem(Task\Checklist\Update\Request $request): Task\Checklist\Update\Response
    {
        try {
            return $this->request
                ->put('api/v3/tasks/{task}/checklist/{item}')
                ->var('task', $request->task)
                ->var('item', $request->item)
                ->body($request)
                ->response()
                ->checkStatus(200)
                ->object(Task\Checklist\Update\Response::class)
            ;
        } finally {
            $this->cache->delete(self::CACHE_TASKS);
        }
    }

    public function createTag(Tag\Create\Request $request): Tag\Create\Response
    {
        try {
            return $this->request
                ->post('api/v3/tags')
                ->body($request)
                ->response()
                ->checkStatus(201)
                ->object(Tag\Create\Response::class)
            ;
        } finally {
            $this->cache->delete(self::CACHE_TAGS);
        }
    }

    public function deleteTag(string $id): void
    {
        try {
            $this->request
                ->delete('api/v3/tags/{id}')
                ->var('id', $id)
                ->response()
                ->checkStatus(200)
            ;
        } finally {
            $this->cache->delete(self::CACHE_TAGS);
            $this->cache->delete(self::CACHE_TASKS);
        }
    }

    public function updateTag(Tag\Update\Request $request): void
    {
        try {
            $this->request
                ->put('api/v3/tags/{id}')
                ->var('id', $request->id)
                ->body($request)
                ->response()
                ->checkStatus(200)
            ;
        } finally {
            $this->cache->delete(self::CACHE_TAGS);
        }
    }

    public function listTags(): Tag\List\Response
    {
        return $this->cache->get(self::CACHE_TAGS, function (CacheItemInterface $item): Tag\List\Response {
            $item->expiresAfter(60 * 60);

            return $this->request
                ->get('api/v3/tags')
                ->response()
                ->checkStatus(200)
                ->object(Tag\List\Response::class)
            ;
        });
    }

    public function getUser(): User\Get\Response
    {
        return $this->request
            ->get('api/v3/user')
            ->response()
            ->checkStatus(200)
            ->object(User\Get\Response::class)
        ;
    }

    public function runCron(): void
    {
        try {
            $this->request
                ->post('api/v3/cron')
                ->response()
                ->checkStatus(200)
            ;
        } finally {
            $this->cache->delete(self::CACHE_TASKS);
        }
    }
}
