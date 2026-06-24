<?php

namespace App\Models;

use Database\Factories\ClientAgreementFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'client_id',
    'agreement_ref',
    'scope_of_work',
    'duration_days',
    'start_date',
    'end_date',
    'monthly_invoice_value',
])]
class ClientAgreement extends Model
{
    /** @use HasFactory<ClientAgreementFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'duration_days' => 'integer',
            'monthly_invoice_value' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<ClientAgreementCrewLine, $this>
     */
    public function crewLines(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ClientAgreementCrewLine::class);
    }
}
