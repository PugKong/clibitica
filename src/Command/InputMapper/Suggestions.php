<?php

declare(strict_types=1);

namespace App\Command\InputMapper;

use Closure;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Completion\Suggestion;

interface Suggestions
{
    /**
     * @return Closure(CompletionInput, CompletionSuggestions): list<string|Suggestion>
     */
    public function suggester(string $name): Closure;
}
