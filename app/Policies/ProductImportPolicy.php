<?php

namespace App\Policies;

use Neon\Admin\Models\Admin;
use App\Models\ProductImport;
  
class ProductImportPolicy
{
  public function viewAny(Admin $user): bool
  {
    return true;
  }

  public function view(Admin $user, ProductImport $product): bool
  {
    return true;
  }

  public function create(Admin $user): bool
  {
    return false;
  }

  public function update(Admin $user, ProductImport $product): bool
  {
    return false;
  }

  public function delete(Admin $user, ProductImport $product): bool
  {
    return true;
  }

  public function restore(Admin $user, ProductImport $product): bool
  {
    return true;
  }

  public function forceDelete(Admin $user, ProductImport $product): bool
  {
    return true;
  }

}