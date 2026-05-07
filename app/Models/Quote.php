<?php

namespace App\Models;

use Database\Factories\QuoteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'doc_no',
    'type',
    'issue_date',
    'expiry_date',
    'status',
    'currency',
    'client_name',
    'client_po',
    'vessel',
    'location',
    'start_date',
    'end_date',
    'payment_terms',
    'scope',
    'total_amount',
])]
class Quote extends Model
{
    /** @use HasFactory<QuoteFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'expiry_date' => 'date',
            'start_date' => 'date',
            'end_date' => 'date',
            'total_amount' => 'decimal:2',
        ];
    }

    /**
     * @return HasMany<QuoteCrewLine, $this>
     */
    public function crewLines(): HasMany
    {
        return $this->hasMany(QuoteCrewLine::class);
    }
}
