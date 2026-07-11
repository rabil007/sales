<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $invoice->doc_no }} Invoice</title>
    <style>
        @page { size: A4; margin: 18mm 14mm 22mm 14mm; }
        body { margin: 0; font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 10.5px; line-height: 1.4; }
        .page { min-height: 252mm; position: relative; page-break-after: always; }
        .page:last-child { page-break-after: auto; }

        /* ── HEADER ─────────────────────────────────────────────── */
        .sheet-header { border: 1px solid #374151; width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .sheet-header td { border: 1px solid #374151; padding: 6px 8px; vertical-align: middle; }
        .logo-cell { width: 22%; text-align: center; }
        .logo-cell img { height: 40px; width: auto; }
        .title-cell { width: 43%; text-align: center; font-size: 13px; font-weight: 700; line-height: 1.4; color: #1e3a8a; text-transform: uppercase; letter-spacing: 0.5px; }
        .meta-cell { width: 35%; font-size: 10px; line-height: 1.65; }
        .meta-cell strong { font-weight: 700; color: #111827; }

        /* ── FOOTER ─────────────────────────────────────────────── */
        .footer-bar {
            position: absolute;
            left: 0; right: 0; bottom: 0;
            border-top: 1px solid #9ca3af;
            padding-top: 5px;
            font-size: 8px;
            color: #374151;
        }
        .footer-company { font-size: 8.5px; font-weight: 700; color: #111827; margin-bottom: 3px; }
        .footer-grid { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .footer-grid td {
            padding: 0 6px 0 0;
            vertical-align: top;
            font-size: 7.6px;
            line-height: 1.25;
        }
        .footer-grid .footer-page {
            text-align: right;
            width: 12%;
            white-space: nowrap;
            vertical-align: middle;
            padding-right: 0;
        }
        .footer-grid .footer-col { width: 22%; white-space: normal; word-break: break-word; }
        .footer-icon { display: inline-block; width: 13px; height: 13px; margin-right: 4px; vertical-align: middle; }

        /* ── CLIENT BLOCK ───────────────────────────────────────── */
        .client-block { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .client-block td { padding: 8px 10px; border: 1px solid #d1d5db; vertical-align: top; }
        .client-title { font-weight: 700; font-size: 10px; text-transform: uppercase; color: #4b5563; margin-bottom: 4px; border-bottom: 1px solid #e5e7eb; padding-bottom: 2px; }
        .client-name { font-weight: 700; font-size: 11.5px; color: #111827; }

        /* ── ITEMS TABLE ────────────────────────────────────────── */
        .tbl { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .tbl th, .tbl td { border: 1px solid #374151; padding: 6px 8px; font-size: 9.5px; vertical-align: middle; }
        .tbl th { background: #dbeafe; font-weight: 700; text-align: center; color: #1e3a8a; text-transform: uppercase; font-size: 9px; }
        .tbl .left { text-align: left; }
        .tbl .center { text-align: center; }
        .tbl .right { text-align: right; }

        /* ── TOTALS TABLE ───────────────────────────────────────── */
        .totals-wrapper { width: 100%; margin-bottom: 20px; }
        .totals-tbl { width: 45%; float: right; border-collapse: collapse; }
        .totals-tbl td { padding: 5px 8px; border: 1px solid #374151; font-size: 10px; }
        .totals-tbl .label { font-weight: 700; text-align: right; background: #f3f4f6; }
        .totals-tbl .val { text-align: right; font-weight: 700; }
        .totals-tbl .grand-total { background: #dbeafe; font-size: 11px; color: #1e3a8a; }

        /* ── INSTRUCTIONS & NOTES ───────────────────────────────── */
        .clearfix::after { content: ""; clear: both; display: table; }
        .box-title { font-weight: 700; font-size: 10px; text-transform: uppercase; color: #1f2937; margin-bottom: 3px; }
        .box-content { border: 1px solid #d1d5db; background: #f9fafb; padding: 8px 10px; font-size: 9.5px; line-height: 1.5; white-space: pre-wrap; margin-bottom: 14px; }
        
        .sign-off { margin-top: 24px; font-size: 10px; line-height: 1.6; }
    </style>
</head>
<body>
    @php
        $cs = \App\Models\CompanySetting::allKeyed();

        $logoAbsolutePath = \App\Models\CompanySetting::logoAbsolutePath();
        $logoDataUri = null;
        if (is_string($logoAbsolutePath) && is_file($logoAbsolutePath)) {
            $logoDataUri = 'data:image/png;base64,'.base64_encode((string) file_get_contents($logoAbsolutePath));
        }
        $companyName      = $cs['company_name']       ?? 'Overseas Marine Services';
        $companyAddress   = $cs['company_address']    ?? '';
        $companyPhone     = $cs['company_phone']      ?? '';
        $companyEmail     = $cs['company_email']      ?? '';
        $companyWebsite   = $cs['company_website']    ?? '';
        $signatoryName    = $cs['signatory_name']     ?? 'Authorized Signatory';
        $signatoryRole    = $cs['signatory_role']     ?? 'Commercial Department';

        $footerIconSvg = [
            'address' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><circle cx="8" cy="8" r="8" fill="#1e40af"/><path d="M8 3.2a2.7 2.7 0 0 0-2.7 2.7c0 2 2.7 5.9 2.7 5.9s2.7-3.9 2.7-5.9A2.7 2.7 0 0 0 8 3.2Zm0 3.6A1.1 1.1 0 1 1 8 4.6a1.1 1.1 0 0 1 0 2.2Z" fill="#fff"/></svg>',
            'phone' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><circle cx="8" cy="8" r="8" fill="#1e40af"/><path d="M5 4.3c.2-.2.5-.2.7 0l1.2 1c.2.2.2.5.1.7l-.6.9a7.5 7.5 0 0 0 2.7 2.7l.9-.6c.2-.1.5-.1.7.1l1 1.2c.2.2.2.5 0 .7l-.6.6c-.6.6-1.4.8-2.2.6a9.7 9.7 0 0 1-4.1-2.7A9.7 9.7 0 0 1 4 5.4c-.2-.8 0-1.6.6-2.2l.4-.5Z" fill="#fff"/></svg>',
            'email' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><circle cx="8" cy="8" r="8" fill="#1e40af"/><path d="M4 5.1h8a.5.5 0 0 1 .5.5v4.8a.5.5 0 0 1-.5.5H4a.5.5 0 0 1-.5-.5V5.6a.5.5 0 0 1 .5-.5Zm0 1 4 2.6 4-2.6" fill="none" stroke="#fff" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'web' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><circle cx="8" cy="8" r="8" fill="#1e40af"/><path d="M8 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm0 0c1.1 1 1.1 7 0 8m0-8c-1.1 1-1.1 7 0 8M4 8h8M4.7 6h6.6" fill="none" stroke="#fff" stroke-width="1.2" stroke-linecap="round"/></svg>',
        ];
        $footerIcons = collect($footerIconSvg)->mapWithKeys(
            fn (string $svg, string $key) => [$key => 'data:image/svg+xml;base64,'.base64_encode($svg)]
        )->all();

        $docNo     = str_replace('-', '/', $invoice->doc_no);
        $issueDate = optional($invoice->issue_date)->format('d M Y');
        $dueDate   = optional($invoice->due_date)->format('d M Y');
        $quoteRef  = $invoice->quote ? str_replace('-', '/', $invoice->quote->doc_no) : null;
        $client    = $invoice->client;
    @endphp

    <div class="page">
        <table class="sheet-header">
            <tr>
                <td class="logo-cell">
                    @if ($logoDataUri)
                        <img src="{{ $logoDataUri }}" alt="{{ $companyName }}">
                    @else
                        <strong style="font-size:12px;color:#1e40af;">{{ $companyName }}</strong>
                    @endif
                </td>
                <td class="title-cell">Commercial Invoice<br><span style="font-size:10px; font-weight:400; color:#4b5563;">Provision of Crew / Marine Services</span></td>
                <td class="meta-cell">
                    <strong>Invoice No.:</strong> {{ $docNo }}<br>
                    <strong>Issue Date:</strong> {{ $issueDate }}<br>
                    @if ($dueDate)
                        <strong>Due Date:</strong> {{ $dueDate }}<br>
                    @endif
                    @if ($quoteRef)
                        <strong>Quote Ref:</strong> {{ $quoteRef }}<br>
                    @endif
                    @if ($invoice->client_po)
                        <strong>PO Number:</strong> {{ $invoice->client_po }}<br>
                    @endif
                    <strong>Status:</strong> {{ $invoice->status }}
                </td>
            </tr>
        </table>

        <table class="client-block">
            <tr>
                <td style="width:55%;">
                    <div class="client-title">Bill To / Client Details</div>
                    <div class="client-name">{{ $invoice->client_name }}</div>
                    @if ($client?->address)
                        <div>{{ $client->address }}</div>
                    @endif
                    @if ($client?->city)
                        <div>{{ $client->city }}</div>
                    @endif
                    @if ($client?->contact_person)
                        <div style="margin-top:4px;"><strong>Attn:</strong> {{ $client->contact_person }} {{ $client->contact_designation ? '('.$client->contact_designation.')' : '' }}</div>
                    @endif
                </td>
                <td style="width:45%;">
                    <div class="client-title">Project & Operational Reference</div>
                    @if ($invoice->project_name)
                        <div><strong>Project:</strong> {{ $invoice->project_name }}</div>
                    @endif
                    @if ($invoice->vessel)
                        <div><strong>Vessel / Asset:</strong> {{ $invoice->vessel }}</div>
                    @endif
                    @if ($invoice->location)
                        <div><strong>Location:</strong> {{ $invoice->location }}</div>
                    @endif
                    <div><strong>Currency:</strong> {{ $invoice->currency }}</div>
                </td>
            </tr>
        </table>

        <table class="tbl">
            <thead>
                <tr>
                    <th style="width:7%;">Sl.</th>
                    <th class="left" style="width:38%;">Description & Service Item</th>
                    <th style="width:15%;">Category</th>
                    <th style="width:8%;">Qty</th>
                    <th style="width:14%;">Duration / Basis</th>
                    <th style="width:14%;">Rate ({{ $invoice->currency }})</th>
                    <th style="width:16%;">Total ({{ $invoice->currency }})</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($invoice->items as $index => $item)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td class="left"><strong>{{ $item->description }}</strong></td>
                        <td class="center">{{ $item->category ?: 'Marine' }}</td>
                        <td class="center">{{ $item->qty }}</td>
                        <td class="center">{{ (float) $item->duration }} {{ $item->duration_unit ?: ($item->basis ?: 'Days') }}</td>
                        <td class="right">{{ number_format((float) $item->rate, 2) }}</td>
                        <td class="right"><strong>{{ number_format((float) $item->line_total, 2) }}</strong></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="center" style="padding:20px 0; color:#6b7280;">No invoice items detailed.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="totals-wrapper clearfix">
            <table class="totals-tbl">
                <tr>
                    <td class="label">Subtotal ({{ $invoice->currency }})</td>
                    <td class="val">{{ number_format((float) $invoice->subtotal, 2) }}</td>
                </tr>
                @if ((float) $invoice->tax_rate > 0 || (float) $invoice->tax_amount > 0)
                    <tr>
                        <td class="label">Tax ({{ (float) $invoice->tax_rate }}%)</td>
                        <td class="val">{{ number_format((float) $invoice->tax_amount, 2) }}</td>
                    </tr>
                @endif
                <tr class="grand-total">
                    <td class="label" style="background:#dbeafe; color:#1e3a8a;">Total Payable ({{ $invoice->currency }})</td>
                    <td class="val" style="background:#dbeafe; color:#1e3a8a;">{{ number_format((float) $invoice->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>

        @if ($invoice->payment_instructions)
            <div class="box-title">Payment Instructions & Bank Details</div>
            <div class="box-content">{{ $invoice->payment_instructions }}</div>
        @endif

        @if ($invoice->notes)
            <div class="box-title">Additional Notes & Remarks</div>
            <div class="box-content">{{ $invoice->notes }}</div>
        @endif

        <div class="sign-off">
            <div style="float:right; width:230px; text-align:center;">
                <div style="font-weight:700; margin-bottom:40px;">For {{ $companyName }}</div>
                <div style="border-top:1px solid #374151; padding-top:4px;">
                    <strong>{{ $signatoryName }}</strong><br>
                    <span style="color:#4b5563; font-size:9.5px;">{{ $signatoryRole }}</span>
                </div>
            </div>
            <div style="clear:both;"></div>
        </div>

        <div class="footer-bar">
            <div class="footer-company">{{ $companyName }}.</div>
            <table class="footer-grid">
                <tr>
                    <td class="footer-col"><img class="footer-icon" src="{{ $footerIcons['address'] }}" alt=""> {{ $companyAddress }}</td>
                    <td class="footer-col"><img class="footer-icon" src="{{ $footerIcons['phone'] }}" alt=""> {{ $companyPhone }}</td>
                    <td class="footer-col"><img class="footer-icon" src="{{ $footerIcons['email'] }}" alt=""> {{ $companyEmail }}</td>
                    <td class="footer-col"><img class="footer-icon" src="{{ $footerIcons['web'] }}" alt=""> {{ $companyWebsite }}</td>
                    <td class="footer-page">Page 1 of 1</td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
