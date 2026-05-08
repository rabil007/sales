<?php

namespace App\Policies;

use App\Models\CompanySetting;
use App\Models\User;

class CompanySettingPolicy
{
    public function manageSettings(User $user, CompanySetting $companySetting): bool
    {
        return true;
    }
}
