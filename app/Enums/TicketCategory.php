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

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $category) => ['value' => $category->value, 'label' => $category->label()],
            self::cases(),
        );
    }
}
