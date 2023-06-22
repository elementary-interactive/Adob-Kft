<?php

namespace App\Policies;

use Neon\Admin\Models\Admin;
use App\Models\Category;
  
class CategoryPolicy
{
  public function viewAny(Admin $user): bool
  {
    return true;
  }

  public function view(Admin $user, Category $category): bool
  {
    return true;
  }

  public function create(Admin $user): bool
  {
    return true;
  }

  public function update(Admin $user, Category $category): bool
  {
    return true;
  }

  public function delete(Admin $user, Category $category): bool
  {
    return true;
  }

  public function restore(Admin $user, Category $category): bool
  {
    return true;
  }

  public function forceDelete(Admin $user, Category $category): bool
  {
    return true;
  }

}