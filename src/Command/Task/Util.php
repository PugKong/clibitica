<?php

declare(strict_types=1);

namespace App\Command\Task;

use App\Habitica\Task\Daily;
use App\Habitica\Task\Repeat as TaskRepeat;
use App\Habitica\Task\Todo;

use function count;
use function in_array;

use const PHP_EOL;

final readonly class Util
{
    /**
     * @param Repeat[] $repeat
     */
    public static function repeatArrayToObject(array $repeat): ?TaskRepeat
    {
        if (0 === count($repeat)) {
            return null;
        }

        return new TaskRepeat(
            su: in_array(Repeat::SUNDAY, $repeat, true),
            m: in_array(Repeat::MONDAY, $repeat, true),
            t: in_array(Repeat::TUESDAY, $repeat, true),
            w: in_array(Repeat::WEDNESDAY, $repeat, true),
            th: in_array(Repeat::THURSDAY, $repeat, true),
            f: in_array(Repeat::FRIDAY, $repeat, true),
            s: in_array(Repeat::SATURDAY, $repeat, true),
        );
    }

    public static function formatChecklist(Todo|Daily $task): string
    {
        $items = [];
        foreach ($task->checklist as $item) {
            $status = $item->completed ? 'x' : ' ';
            $items[] = "[$status] $item->text";
        }

        return implode(PHP_EOL, $items);
    }
}
