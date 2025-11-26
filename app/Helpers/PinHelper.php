<?php

namespace App\Helpers;

use App\Models\User;
use Carbon\Carbon;

class PinHelper
{
    /**
     * Obtém informações do PIN do usuário
     */
    public static function getPinInfo(User $user): array
    {
        $createdAt = $user->pin_created_at;
        if ($createdAt && is_string($createdAt)) {
            $createdAt = Carbon::parse($createdAt);
        }
        
        return [
            'has_pin' => !empty($user->pin),
            'is_active' => $user->pin_active,
            'created_at' => $createdAt ? $createdAt->format('d/m/Y H:i') : null,
            'days_since_creation' => $createdAt ? $createdAt->diffInDays(Carbon::now()) : null
        ];
    }
}
