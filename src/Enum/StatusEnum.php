<?php

declare(strict_types=1);

namespace App\Enum;

enum StatusEnum: string
{
    case ACTIVE = 'ACT';
    case NEW = 'NEW';
    case CLOSED = 'END';

    public function getLabel(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::NEW => 'New',
            self::CLOSED => 'Closed',
        };
    }

    public static function fromString(string $value): self
    {
        return match ($value) {
            'ACT' => self::ACTIVE,
            'NEW' => self::NEW,
            'END' => self::CLOSED,
            default => throw new \InvalidArgumentException("Invalid status value: $value"),
        };
    }
}
