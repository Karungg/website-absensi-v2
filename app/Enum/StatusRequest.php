<?php

namespace App\Enum;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum StatusRequest: string implements HasLabel, HasColor, HasIcon
{
    case Zero = 'zero';
    case One = 'one';
    case Two = 'two';
    case Three = 'three';
    case Four = 'four';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Zero => 'Pending',
            self::One => 'Disetujui Kepala Unit',
            self::Two => 'Disetujui SDM',
            self::Three => 'Disetujui Kepala Balai',
            self::Four => 'Ditolak'
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Zero => 'gray',
            self::One => 'warning',
            self::Two => 'info',
            self::Three => 'success',
            self::Four => 'danger'
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Zero => 'heroicon-m-arrow-path',
            self::One => 'heroicon-m-hand-thumb-up',
            self::Two => 'heroicon-m-hand-thumb-up',
            self::Three => 'heroicon-m-hand-thumb-up',
            self::Four => 'heroicon-m-x-mark',
        };
    }
}
