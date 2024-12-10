<?php

declare(strict_types=1);

namespace App\Habitica\Task\Create;

final readonly class RequestRepeat
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
