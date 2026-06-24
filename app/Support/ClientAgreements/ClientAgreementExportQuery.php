<?php

namespace App\Support\ClientAgreements;

use App\Models\ClientAgreement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ClientAgreementExportQuery
{
    public static function fromRequest(Request $request): Builder
    {
        $q = $request->string('q')->trim()->toString();
        $clientId = $request->string('client_id')->toString();
        $status = $request->string('status')->trim()->toString();

        return ClientAgreement::query()
            ->with('client')
            ->when($q !== '', fn (Builder $builder) => $builder->where(function (Builder $builder) use ($q): void {
                $builder->where('agreement_ref', 'like', "%{$q}%")
                    ->orWhere('scope_of_work', 'like', "%{$q}%")
                    ->orWhereHas('client', fn (Builder $builder) => $builder->where('name', 'like', "%{$q}%"));
            }))
            ->when($clientId !== '', fn (Builder $builder) => $builder->where('client_id', (int) $clientId))
            ->when($status === 'active', fn (Builder $builder) => $builder->whereDate('end_date', '>=', now()->toDateString()))
            ->when($status === 'expired', fn (Builder $builder) => $builder->whereDate('end_date', '<', now()->toDateString()))
            ->orderBy('id');
    }
}
