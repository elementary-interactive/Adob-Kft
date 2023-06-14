<?php

namespace App\Policies;

use Neon\Admin\Models\Admin;
use Neon\Models\Menu;
  
class MenuPolicy
{
  public function viewAny(Admin $user): bool
  {
    return true;
  }

  public function view(Admin $user, Menu $menu): bool
  {
    return true;
  }

  public function create(Admin $user): bool
  {
    return true;
  }

  public function update(Admin $user, Menu $menu): bool
  {
    return true;
  }

  public function delete(Admin $user, Menu $menu): bool
  {
    return true;
  }

  public function restore(Admin $user, Menu $menu): bool
  {
    return true;
  }

  public function forceDelete(Admin $user, Menu $menu): bool
  {
    return true;
  }

}