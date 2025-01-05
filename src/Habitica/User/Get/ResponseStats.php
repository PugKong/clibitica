<?php

declare(strict_types=1);

namespace App\Habitica\User\Get;

final readonly class ResponseStats
{
    public function __construct(
        public string $class,
        public int $lvl,
        public int $hp,
        public int $maxHealth,
        public int $mp,
        public int $maxMP,
        public int $exp,
        public int $toNextLevel,
        public int $str,
        public int $con,
        public int $int,
        public int $per,
        public float $gp,
    ) {
    }
}
