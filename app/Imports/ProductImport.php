<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\Importable;

class ProductImport implements ToArray
{
    use Importable;

    public function array(array $row)
    {
        return $row;
    }
}