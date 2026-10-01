<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

/** Admins are allowed everything via Gate::before in AppServiceProvider. */
class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isFarmer();
    }

    public function create(User $user): bool
    {
        return $user->isFarmer() && $user->farmerProfile !== null;
    }

    public function update(User $user, Product $product): bool
    {
        return $this->owns($user, $product);
    }

    public function delete(User $user, Product $product): bool
    {
        return $this->owns($user, $product);
    }

    private function owns(User $user, Product $product): bool
    {
        return $user->isFarmer()
            && $user->farmerProfile !== null
            && $product->farmer_profile_id === $user->farmerProfile->id;
    }
}
