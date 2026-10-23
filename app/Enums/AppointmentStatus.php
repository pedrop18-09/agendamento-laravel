<?php

namespace App\Enums;

enum AppointmentStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';
    case Completed = 'completed';
    case NoShow = 'no_show';

        /**
     * Status para os quais este status pode mudar.
     *
     * @return array<self>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Pending => [self::Confirmed, self::Cancelled],
            self::Confirmed => [self::Completed, self::NoShow, self::Cancelled],
            default => [],
        };
    }

    public function canChangeTo(self $new): bool
    {
        return in_array($new, $this->allowedTransitions(), true);
    }
}
