<?php

namespace App\Support\ClientAgreements;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientAgreementSpreadsheetTemplateExporter
{
    public function download(string $filename): StreamedResponse
    {
        return response()->streamDownload(function (): void {
            $spreadsheet = new Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Import Template');

            foreach (ClientAgreementSpreadsheetColumns::IMPORT_HEADERS as $columnIndex => $header) {
                $sheet->getCell([$columnIndex + 1, 1])->setValue($header);
            }

            $lastColumn = chr(ord('A') + count(ClientAgreementSpreadsheetColumns::IMPORT_HEADERS) - 1);
            $sheet->getStyle("A1:{$lastColumn}1")->getFont()->setBold(true);
            $sheet->getStyle("A1:{$lastColumn}1")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('FFDBEAFE');

            $sheet->fromArray([
                'ADNOC Offshore',
                'OMS-AGR-2026-001',
                'Offshore crew supply services.',
                30,
                '2026-06-01',
                12500.00,
            ], null, 'A2');

            $sheet->getStyle('A2:F2')->getFont()->getColor()->setARGB('FF9CA3AF');

            foreach (range('A', $lastColumn) as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
