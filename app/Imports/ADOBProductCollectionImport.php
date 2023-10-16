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
  use Importable;

  const HEADING_ROW = 1;

  const MAX_SUB_CATEGORY_COUNT    = 5;

  static $columns = \App\Models\Columns\ADOBProductsImportColumns::class;


  public function toArray()
  {

  }
}
