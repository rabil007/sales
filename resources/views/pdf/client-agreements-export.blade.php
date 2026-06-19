<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client Agreements</title>
    <style>
        @page { size: A4 landscape; margin: 12mm; }
        body { margin: 0; font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 10px; line-height: 1.4; }
        .header { margin-bottom: 14px; }
        .title { font-size: 16px; font-weight: 700; margin: 0 0 4px; }
        .meta { font-size: 9px; color: #4b5563; }
        .tbl { width: 100%; border-collapse: collapse; }
        .tbl th, .tbl td { border: 1px solid #374151; padding: 5px 6px; vertical-align: top; }
        .tbl th { background: #dbeafe; font-weight: 700; text-align: center; font-size: 9px; }
        .tbl .center { text-align: center; }
        .tbl .right { text-align: right; }
        .tbl .left { text-align: left; }
        .scope { max-width: 180px; word-wrap: break-word; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">{{ $appName }} — Client Agreements</h1>
        <p class="meta">Generated on {{ $generatedAt->format('d M Y, H:i') }} · {{ $agreements->count() }} record(s)</p>
    </div>

    <table class="tbl">
        <thead>
            <tr>
                <th style="width: 5%;">Sl. No.</th>
                <th style="width: 14%;">Client Name</th>
                <th style="width: 12%;">Agreement Ref.</th>
                <th style="width: 24%;">Scope of Work</th>
                <th style="width: 8%;">Duration<br>(days)</th>
                <th style="width: 10%;">Start Date</th>
                <th style="width: 10%;">End Date</th>
                <th style="width: 12%;">Monthly Invoice<br>Value (USD)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($agreements as $agreement)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td class="left">{{ $agreement->client->name }}</td>
                    <td class="left">{{ $agreement->agreement_ref }}</td>
                    <td class="left scope">{{ $agreement->scope_of_work ?: '-' }}</td>
                    <td class="center">{{ number_format($agreement->duration_days) }}</td>
                    <td class="center">{{ $agreement->start_date->format('d M Y') }}</td>
                    <td class="center">{{ $agreement->end_date->format('d M Y') }}</td>
                    <td class="right">{{ number_format($agreement->monthly_invoice_value, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="center">No client agreements found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
