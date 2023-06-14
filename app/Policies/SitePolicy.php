<?php

namespace App\Policies;

use Neon\Admin\Models\Admin;
use Neon\Site\Models\Site;
  
class SitePolicy
{
  public function viewAny(Admin $user): bool
  {
    return true;
  }

  public function view(Admin $user, Site $site): bool
  {
    return true;
  }

  public function create(Admin $user): bool
  {
    return true;
  }

  public function update(Admin $user, Site $site): bool
  {
    return true;
  }

  public function delete(Admin $user, Site $site): bool
  {
    return true;
  }

  public function restore(Admin $user, Site $site): bool
  {
    return true;
  }

  public function forceDelete(Admin $user, Site $site): bool
  {
    return true;
  }

}