<?php

namespace App\Support\ClientAgreements;

use App\Models\Client;
use App\Models\ClientAgreement;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClientAgreementSpreadsheetImporter
{
    public function import(UploadedFile $file): ClientAgreementImportResult
    {
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $headerMap = $this->mapHeaders($sheet);

        if ($headerMap === null) {
            return new ClientAgreementImportResult(
                failed: 1,
                errors: ['Row 1: Invalid template headers. Download the import template and try again.'],
            );
        }

        $result = new ClientAgreementImportResult;
        $seenAgreementRefs = [];
        $highestRow = (int) $sheet->getHighestDataRow();

        for ($rowNumber = 2; $rowNumber <= $highestRow; $rowNumber++) {
            $row = $this->extractRow($sheet, $headerMap, $rowNumber);

            if ($this->isEmptyRow($row)) {
                continue;
            }

            $error = $this->validateAndCreateRow($row, $rowNumber, $seenAgreementRefs);

            if ($error !== null) {
                $result->failed++;
                $result->errors[] = $error;

                continue;
            }

            $result->imported++;
        }

        return $result;
    }

    /**
     * @return array<string, int>|null
     */
    private function mapHeaders(Worksheet $sheet): ?array
    {
        $headerMap = [];
        $highestColumn = $sheet->getHighestDataColumn();

        for ($columnIndex = 1; $columnIndex <= Coordinate::columnIndexFromString($highestColumn); $columnIndex++) {
            $header = trim((string) $sheet->getCell([$columnIndex, 1])->getValue());

            if ($header !== '') {
                $headerMap[$header] = $columnIndex;
            }
        }

        foreach (ClientAgreementSpreadsheetColumns::IMPORT_HEADERS as $requiredHeader) {
            if (! array_key_exists($requiredHeader, $headerMap)) {
                return null;
            }
        }

        return $headerMap;
    }

    /**
     * @param  array<string, int>  $headerMap
     * @return array<string, mixed>
     */
    private function extractRow(Worksheet $sheet, array $headerMap, int $rowNumber): array
    {
        $row = [];

        foreach ($headerMap as $header => $columnIndex) {
            $row[$header] = $sheet->getCell([$columnIndex, $rowNumber])->getValue();
        }

        return $row;
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  array<string, true>  $seenAgreementRefs
     */
    private function validateAndCreateRow(array $row, int $rowNumber, array &$seenAgreementRefs): ?string
    {
        $clientName = trim((string) ($row[ClientAgreementSpreadsheetColumns::CLIENT_NAME] ?? ''));
        $agreementRef = trim((string) ($row[ClientAgreementSpreadsheetColumns::AGREEMENT_REF] ?? ''));
        $scopeOfWork = trim((string) ($row[ClientAgreementSpreadsheetColumns::SCOPE_OF_WORK] ?? ''));
        $durationRaw = $row[ClientAgreementSpreadsheetColumns::DURATION_DAYS] ?? null;
        $startDateRaw = $row[ClientAgreementSpreadsheetColumns::START_DATE] ?? null;
        $monthlyValueRaw = $row[ClientAgreementSpreadsheetColumns::MONTHLY_INVOICE_VALUE] ?? null;

        if ($clientName === '') {
            return "Row {$rowNumber}: Client Name is required.";
        }

        if ($agreementRef === '') {
            return "Row {$rowNumber}: Agreement Ref. is required.";
        }

        $client = Client::query()->where('name', $clientName)->first();

        if ($client === null) {
            return "Row {$rowNumber}: Client \"{$clientName}\" not found.";
        }

        if (isset($seenAgreementRefs[$agreementRef])) {
            return "Row {$rowNumber}: Agreement Ref. \"{$agreementRef}\" is duplicated in this file.";
        }

        if (ClientAgreement::query()->where('agreement_ref', $agreementRef)->exists()) {
            return "Row {$rowNumber}: Agreement Ref. \"{$agreementRef}\" already exists.";
        }

        if (! is_numeric($durationRaw) || (int) $durationRaw < 1) {
            return "Row {$rowNumber}: Duration (days) must be an integer of at least 1.";
        }

        $durationDays = (int) $durationRaw;

        try {
            $startDate = $this->parseDate($startDateRaw);
        } catch (\Throwable) {
            return "Row {$rowNumber}: Start Date is invalid.";
        }

        if (! is_numeric($monthlyValueRaw) || (float) $monthlyValueRaw < 0) {
            return "Row {$rowNumber}: Monthly Invoice Value (USD) must be a number of at least 0.";
        }

        $endDate = $startDate->copy()->addDays(max($durationDays - 1, 0));

        ClientAgreement::query()->create([
            'client_id' => $client->id,
            'agreement_ref' => $agreementRef,
            'scope_of_work' => $scopeOfWork !== '' ? $scopeOfWork : null,
            'duration_days' => $durationDays,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'monthly_invoice_value' => round((float) $monthlyValueRaw, 2),
        ]);

        $seenAgreementRefs[$agreementRef] = true;

        return null;
    }

    private function parseDate(mixed $value): Carbon
    {
        if ($value === null || $value === '') {
            throw new \InvalidArgumentException('Date is required.');
        }

        if (is_numeric($value)) {
            return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $value));
        }

        return Carbon::parse((string) $value);
    }
}
