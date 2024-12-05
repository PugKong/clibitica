<?php

declare(strict_types=1);

namespace App\Habitica;

use App\Http\Http;

final readonly class Habitica
{
    public function __construct(private Http $http)
    {
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
