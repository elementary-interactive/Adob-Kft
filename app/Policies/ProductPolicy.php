<?php

namespace App\Policies;

use Neon\Admin\Models\Admin;
use App\Models\Product;
  
class ProductPolicy
{
  public function viewAny(Admin $user): bool
  {
    return true;
  }

  public function view(Admin $user, Product $product): bool
  {
    return true;
  }

  public function create(Admin $user): bool
  {
    return true;
  }

  public function update(Admin $user, Product $product): bool
  {
    return true;
  }

  public function delete(Admin $user, Product $product): bool
  {
    return true;
  }

  public function restore(Admin $user, Product $product): bool
  {
    return true;
  }

  public function forceDelete(Admin $user, Product $product): bool
  {
    return true;
  }

}