<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\Columns;
use App\Models\ProductImport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToArray;


class ADOBProductCollectionImport implements ToArray
{
  public function array(array $row): array
  {
    return $row;
  }
}
