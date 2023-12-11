<?php

namespace App\Models;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Neon\Models\Statuses\BasicStatus;

enum Status: string implements HasColor, HasIcon, HasLabel
{
    case Active     = 'A'; // BasicStatus::Active->value;
    case Inactive   = 'I'; // BasicStatus::Inactive->value;
    case New        = 'N'; // BasicStatus::New->value;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Active => 'Aktív',
            self::Inactive => 'Inaktív',
            self::New => 'Új',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::New      => 'gray',
            self::Active   => 'success',
            self::Inactive => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::New      => 'heroicon-o-sparkles',
            self::Active   => 'heroicon-o-check-circle',
            self::Inactive => 'heroicon-o-x-circle',
        };
    }
}