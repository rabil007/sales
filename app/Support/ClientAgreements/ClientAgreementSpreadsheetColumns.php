<?php

namespace App\Support\ClientAgreements;

class ClientAgreementSpreadsheetColumns
{
    /** @var array<int, string> */
    public const EXPORT_HEADERS = [
        'Sl. No.',
        'Client Name',
        'Agreement Ref.',
        'Scope of Work',
        'Duration (days)',
        'Start Date',
        'End Date',
        'Monthly Invoice Value (USD)',
    ];

    /** @var array<int, string> */
    public const IMPORT_HEADERS = [
        'Client Name',
        'Agreement Ref.',
        'Scope of Work',
        'Duration (days)',
        'Start Date',
        'Monthly Invoice Value (USD)',
    ];

    public const CLIENT_NAME = 'Client Name';

    public const AGREEMENT_REF = 'Agreement Ref.';

    public const SCOPE_OF_WORK = 'Scope of Work';

    public const DURATION_DAYS = 'Duration (days)';

    public const START_DATE = 'Start Date';

    public const END_DATE = 'End Date';

    public const MONTHLY_INVOICE_VALUE = 'Monthly Invoice Value (USD)';
}
