<?php

declare(strict_types=1);

namespace App\Habitica\Tag\List;

final readonly class Response
{
    /**
     * @param ResponseData[] $data
     */
    public function __construct(public array $data)
    {
    }
}
