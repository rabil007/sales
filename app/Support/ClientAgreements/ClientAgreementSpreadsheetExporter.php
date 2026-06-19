<?php

namespace App\Support\ClientAgreements;

use App\Models\ClientAgreement;
use Illuminate\Database\Eloquent\Builder;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientAgreementSpreadsheetExporter
{
    /** @var array<int, string> */
    private const HEADERS = [
        'Sl. No.',
        'Client Name',
        'Agreement Ref.',
        'Scope of Work',
        'Duration (days)',
        'Start Date',
        'End Date',
        'Monthly Invoice Value (USD)',
    ];

    public function download(Builder $query, string $filename): StreamedResponse
    {
        $agreements = $query->get();

        return response()->streamDownload(function () use ($agreements): void {
            $spreadsheet = new Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Client Agreements');

            foreach (self::HEADERS as $columnIndex => $header) {
                $cell = $sheet->getCell([$columnIndex + 1, 1]);
                $cell->setValue($header);
            }

            $sheet->getStyle('A1:H1')->getFont()->setBold(true);
            $sheet->getStyle('A1:H1')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('FFDBEAFE');

            /** @var ClientAgreement $agreement */
            foreach ($agreements as $index => $agreement) {
                $row = $index + 2;

                $sheet->fromArray([
                    $index + 1,
                    $agreement->client->name,
                    $agreement->agreement_ref,
                    $agreement->scope_of_work,
                    $agreement->duration_days,
                    $agreement->start_date->format('Y-m-d'),
                    $agreement->end_date->format('Y-m-d'),
                    (float) $agreement->monthly_invoice_value,
                ], null, "A{$row}");
            }

            foreach (range('A', 'H') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            $sheet->getStyle('H2:H'.$sheet->getHighestRow())
                ->getNumberFormat()
                ->setFormatCode('#,##0.00');

            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
