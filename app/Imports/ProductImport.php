<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\Importable;

class ProductImport implements ToModel
{
    use Importable;

    public function toArray(array $row)
    {
        return $row;
    }
}