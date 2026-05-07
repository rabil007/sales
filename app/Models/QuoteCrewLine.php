<?php

namespace App\Models;

use Database\Factories\QuoteCrewLineFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'quote_id',
    'rank',
    'category',
    'qty',
    'basis',
    'rate',
    'duration',
    'ot_rate',
    'mob_date',
    'remarks',
])]
class QuoteCrewLine extends Model
{
    /** @use HasFactory<QuoteCrewLineFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Quote, $this>
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }
}
