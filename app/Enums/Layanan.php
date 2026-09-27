<?php

namespace App\Enums;

enum Layanan: string
{
    case SIAPPAKAI = 'siappakai';
    case PREMIUM = 'premium';
    case UMUM = 'umum';

    /**
     * Get all available layanan options as an array.
     *
     * @return array<string, string>
     */
    public static function toArray(): array
    {
        return [
            self::SIAPPAKAI->value => 'Siappakai',
            self::PREMIUM->value => 'Premium',
            self::UMUM->value => 'Umum',
        ];
    }

    /**
     * Get the label for the layanan.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::SIAPPAKAI => 'Siappakai',
            self::PREMIUM => 'Premium',
            self::UMUM => 'Umum',
        };
    }

    /**
     * Get all layanan values.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
