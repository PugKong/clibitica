<?php

declare(strict_types=1);

namespace App\Command\Task;

use App\Command\InputMapper\Attribute\Argument;
use App\Command\Suggestions;

final readonly class ChecklistInput
{
    public function __construct(
        #[Argument('id', 'The task id or alias', Suggestions::CHECKLIST_TASK_ID)]
        public string $task,
        #[Argument('action', 'The action: "add", "delete" or "toggle"')]
        public ChecklistAction $action,
        #[Argument('item text or id', 'The text for the new item or its id', Suggestions::CHECKLIST_ITEM_ID)]
        public string $itemTextOrId,
    ) {
    }
}
