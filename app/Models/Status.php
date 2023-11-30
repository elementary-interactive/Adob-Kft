<?php

namespace App\Models;

use Filament\Support\Contracts\HasLabel;
 
enum Status: string implements HasLabel
{
    case Active     = 'A';
    case Inactive   = 'I';
    case New        = 'N';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Active => 'Aktív',
            self::Inactive => 'Inaktív',
            self::New => 'Új',
        };
    }
}