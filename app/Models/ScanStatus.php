<?php

namespace App\Models;

enum ScanStatus: string
{
    case ONGOING = 'ongoing';
    case COMPLETED = 'completed';
    case HIATUS = 'hiatus';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::ONGOING => 'Ongoing',
            self::COMPLETED => 'Completed',
            self::HIATUS => 'Hiatus',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::ONGOING => 'emerald-800',
            self::COMPLETED => 'green-800',
            self::HIATUS => 'red-800',
            self::CANCELLED => 'orange-800',
        };
    }

    public static function fromLabel(string $label): self
    {
        return match($label) {
            'Ongoing' => self::ONGOING,
            'Completed' => self::COMPLETED,
            'Hiatus' => self::HIATUS,
            'Cancelled' => self::CANCELLED,
            default => throw new \ValueError("Unknown label: {$label}"),
        };
    }

    public static function all(): array
    {
        return [
            self::ONGOING,
            self::COMPLETED,
            self::HIATUS,
            self::CANCELLED,
        ];
    }
}