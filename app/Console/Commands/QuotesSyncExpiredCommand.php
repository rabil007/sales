<?php

namespace App\Console\Commands;

use App\Models\Quote;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('quotes:sync-expired')]
#[Description('Set quote status to Expired when expiry_date is in the past.')]
class QuotesSyncExpiredCommand extends Command
{
    public function handle(): int
    {
        $updated = Quote::markPastExpiryAsExpired();

        $this->info("Marked {$updated} quote(s) as Expired.");

        return self::SUCCESS;
    }
}
