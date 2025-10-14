<?php

namespace App\Models;

enum ReadingStatus: string
{
    case NOT_STARTED = 'not_started';
    case ONGOING = 'ongoing';
    case COMPLETED = 'completed';
    case ON_HOLD = 'on_hold';
    case DROPPED = 'dropped';

    public function label(): string
    {
        return match($this) {
            self::NOT_STARTED => 'Not Started',
            self::ONGOING => 'Ongoing',
            self::COMPLETED => 'Completed',
            self::ON_HOLD => 'On Hold',
            self::DROPPED => 'Dropped',
        };
    }

    public static function fromLabel(string $label): self
    {
        return match($label) {
            'Not Started' => self::NOT_STARTED,
            'Ongoing' => self::ONGOING,
            'Completed' => self::COMPLETED,
            'On Hold' => self::ON_HOLD,
            'Dropped' => self::DROPPED,
            default => throw new \ValueError("Unknown label: {$label}"),
        };
    }

    public function color(): string
    {
        return match($this) {
            self::NOT_STARTED => 'gray-800',
            self::ONGOING => 'emerald-800',
            self::COMPLETED => 'green-800',
            self::ON_HOLD => 'yellow-800',
            self::DROPPED => 'red-800',
        };
    }

    public static function all(): array
    {
        return [
            self::NOT_STARTED,
            self::ONGOING,
            self::COMPLETED,
            self::ON_HOLD,
            self::DROPPED,
        ];
    }
}