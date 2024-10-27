<?php

namespace App\Enum;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum TypeRequest: string implements HasLabel, HasColor, HasIcon
{
    case Leave = 'leave';
    case BigHoliday = 'bigHoliday';
    case ImportantLeave = 'importantLeave';
    case GiveBirth = 'giveBirth';
    case Sick = 'sick';
    case LeaveOutside = 'leaveOutside';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Leave => 'Cuti Tahunan',
            self::BigHoliday => 'Cuti Besar',
            self::ImportantLeave => 'Cuti Penting',
            self::GiveBirth => 'Cuti Melahirkan',
            self::Sick => 'Cuti Sakit',
            self::LeaveOutside => 'Cuti Diluar Tanggungan Negara'
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Leave => 'primary',
            self::BigHoliday => 'info',
            self::ImportantLeave => 'danger',
            self::GiveBirth => 'success',
            self::Sick => 'danger',
            self::LeaveOutside => 'gray'
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Leave => 'heroicon-m-arrow-right-start-on-rectangle',
            self::BigHoliday => 'heroicon-m-calendar',
            self::ImportantLeave => 'heroicon-m-exclamation-circle',
            self::GiveBirth => 'heroicon-m-home',
            self::Sick => 'heroicon-m-user-minus',
            self::LeaveOutside => 'heroicon-m-hand-raised'
        };
    }
}
