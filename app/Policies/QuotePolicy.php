<?php

namespace App\Policies;

use App\Models\Quote;
use App\Models\User;

/**
 * Single-tenant ERP: any authenticated user may manage quotes.
 * Refine with roles (e.g. is_admin) when you add multi-role support.
 */
class QuotePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Quote $quote): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Quote $quote): bool
    {
        return true;
    }

    public function delete(User $user, Quote $quote): bool
    {
        return true;
    }

    public function renew(User $user, Quote $quote): bool
    {
        return $this->create($user);
    }
}
