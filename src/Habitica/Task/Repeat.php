<?php

declare(strict_types=1);

namespace App\Habitica\Task;

final readonly class Repeat
{
    public function __construct(
        public bool $su,
        public bool $m,
        public bool $t,
        public bool $w,
        public bool $th,
        public bool $f,
        public bool $s,
    ) {
    }
}
