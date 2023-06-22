<?php

namespace App\Policies;

use Neon\Admin\Models\Admin;
use App\Models\Brand;
  
class BrandPolicy
{
  public function viewAny(Admin $user): bool
  {
    return true;
  }

  public function view(Admin $user, Brand $brand): bool
  {
    return true;
  }

  public function create(Admin $user): bool
  {
    return true;
  }

  public function update(Admin $user, Brand $brand): bool
  {
    return true;
  }

  public function delete(Admin $user, Brand $brand): bool
  {
    return true;
  }

  public function restore(Admin $user, Brand $brand): bool
  {
    return true;
  }

  public function forceDelete(Admin $user, Brand $brand): bool
  {
    return true;
  }

}