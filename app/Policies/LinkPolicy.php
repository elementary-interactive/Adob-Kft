<?php

namespace App\Policies;

use Neon\Admin\Models\Admin;
use Neon\Models\Link;
  
class LinkPolicy
{
  public function viewAny(Admin $user): bool
  {
    return true;
  }

  public function view(Admin $user, Link $link): bool
  {
    return true;
  }

  public function create(Admin $user): bool
  {
    return true;
  }

  public function update(Admin $user, Link $link): bool
  {
    return true;
  }

  public function delete(Admin $user, Link $link): bool
  {
    return true;
  }

  public function restore(Admin $user, Link $link): bool
  {
    return true;
  }

  public function forceDelete(Admin $user, Link $link): bool
  {
    return true;
  }

}