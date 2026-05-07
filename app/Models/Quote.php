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
     * @return HasMany<QuoteCrewLine, $this>
     */
    public function crewLines(): HasMany
    {
        return $this->hasMany(QuoteCrewLine::class);
    }
}
