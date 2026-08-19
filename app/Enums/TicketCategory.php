<?php

namespace App\Enums;

enum TicketCategory: string
{
    case Bug = 'bug';
    case Feature = 'feature';
    case Support = 'support';

    public function label(): string
    {
        return match ($this) {
            self::Bug => 'Bug',
            self::Feature => 'Feature',
            self::Support => 'Support',
        };
    }
}
