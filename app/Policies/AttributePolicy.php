<?php

namespace App\Policies;

use Neon\Admin\Models\Admin;
use Neon\Attributable\Models\Attribute;

class AttributePolicy
{
  public function viewAny(Admin $user): bool
  {
    return true;
  }

  public function view(Admin $user, Attribute $attribute): bool
  {
    return true;
  }

  public function create(Admin $user): bool
  {
    return true;
  }

  public function update(Admin $user, Attribute $attribute): bool
  {
    return true;
  }

  public function delete(Admin $user, Attribute $attribute): bool
  {
    return true;
  }

  public function restore(Admin $user, Attribute $attribute): bool
  {
    return true;
  }

  public function forceDelete(Admin $user, Attribute $attribute): bool
  {
    return true;
  }

}