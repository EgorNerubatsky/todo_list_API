<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * @method static self todo()
 * @method static self inProgress()
 * @method static self done()
 */
class TaskStatus extends Enum
{
    protected static function values(): array
    {
        return [
            'todo' => 'todo',
            'done' => 'done',
        ];
    }
}
