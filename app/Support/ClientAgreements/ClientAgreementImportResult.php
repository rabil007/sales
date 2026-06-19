<?php

namespace App\Support\ClientAgreements;

class ClientAgreementImportResult
{
    /**
     * @param  array<int, string>  $errors
     */
    public function __construct(
        public int $imported = 0,
        public int $failed = 0,
        public array $errors = [],
    ) {}

    public function toFlashMessage(): string
    {
        if ($this->imported === 0 && $this->failed === 0) {
            return 'No rows found to import.';
        }

        $message = "Imported {$this->imported} agreement(s).";

        if ($this->failed > 0) {
            $message .= " {$this->failed} row(s) failed.";
        }

        return $message;
    }
}
