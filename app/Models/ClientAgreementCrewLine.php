<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'client_agreement_id',
    'rank',
    'category',
    'qty',
    'basis',
    'rate',
    'monthly_rate',
    'duration',
    'duration_days',
    'duration_months',
    'manual_total',
    'ot_rate',
    'mob_date',
    'demob_date',
    'remarks',
    'line_total',
])]
class ClientAgreementCrewLine extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'duration' => 'integer',
            'duration_days' => 'integer',
            'duration_months' => 'integer',
            'rate' => 'decimal:2',
            'monthly_rate' => 'decimal:2',
            'manual_total' => 'decimal:2',
            'ot_rate' => 'decimal:2',
            'mob_date' => 'date',
            'demob_date' => 'date',
            'line_total' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<ClientAgreement, $this>
     */
    public function clientAgreement(): BelongsTo
    {
        return $this->belongsTo(ClientAgreement::class);
    }
}
