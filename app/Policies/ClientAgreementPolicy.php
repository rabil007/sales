<?php

namespace App\Policies;

use App\Models\ClientAgreement;
use App\Models\User;

class ClientAgreementPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ClientAgreement $clientAgreement): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ClientAgreement $clientAgreement): bool
    {
        return true;
    }

    public function delete(User $user, ClientAgreement $clientAgreement): bool
    {
        return true;
    }
}
