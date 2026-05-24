<?php

namespace App\Enums;

enum TaskPriority: string
{
    case Low    = 'low';
    case Medium = 'medium';
    case High   = 'high';

    /**
     * Get a human-readable label.
     */
    public function label(): string
    {
        return match($this) {
            self::Low    => 'Low',
            self::Medium => 'Medium',
            self::High   => 'High',
        };
    }

    /**
     * Get Tailwind CSS badge color class.
     */
    public function badgeClass(): string
    {
        return match($this) {
            self::Low    => 'bg-green-100 text-green-800',
            self::Medium => 'bg-yellow-100 text-yellow-800',
            self::High   => 'bg-red-100 text-red-800',
        };
    }
}
