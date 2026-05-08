<?php

namespace App\Policies;

use App\Models\Rank;
use App\Models\User;

class RankPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Rank $rank): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Rank $rank): bool
    {
        return true;
    }

    public function delete(User $user, Rank $rank): bool
    {
        return true;
    }
}
