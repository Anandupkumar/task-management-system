<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Pending    = 'pending';
    case InProgress = 'in_progress';
    case Completed  = 'completed';

    /**
     * Get a human-readable label.
     */
    public function label(): string
    {
        return match($this) {
            self::Pending    => 'Pending',
            self::InProgress => 'In Progress',
            self::Completed  => 'Completed',
        };
    }

    /**
     * Get Tailwind CSS badge color class.
     */
    public function badgeClass(): string
    {
        return match($this) {
            self::Pending    => 'bg-gray-100 text-gray-800',
            self::InProgress => 'bg-blue-100 text-blue-800',
            self::Completed  => 'bg-green-100 text-green-800',
        };
    }
}
