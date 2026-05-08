<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $quote->doc_no }} Proposal</title>
    <style>
        @page { size: A4; margin: 18mm 14mm 22mm 14mm; }
        body { margin: 0; font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 10.5px; line-height: 1.4; }
        .page { min-height: 252mm; position: relative; page-break-after: always; }
        .page:last-child { page-break-after: auto; }

        /* ── HEADER ─────────────────────────────────────────────── */
        .sheet-header { border: 1px solid #374151; width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .sheet-header td { border: 1px solid #374151; padding: 6px 8px; vertical-align: middle; }
        .logo-cell { width: 20%; text-align: center; }
        .logo-cell img { height: 38px; width: auto; }
        .title-cell { width: 45%; text-align: center; font-size: 11.5px; font-weight: 700; line-height: 1.5; }
        .meta-cell { width: 35%; font-size: 10px; line-height: 1.7; }
        .meta-cell strong { font-weight: 700; }

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

        /* ── BODY TEXT ──────────────────────────────────────────── */
        .to-block { margin: 10px 0 10px; font-size: 10.5px; line-height: 1.55; }
        .subject { margin: 8px 0 12px; font-weight: 700; font-size: 10.5px; }
        .section { margin-bottom: 8px; font-size: 10.5px; }
        .section-title { font-weight: 700; margin-bottom: 3px; }
        .bullet-list { margin: 2px 0 5px 16px; padding: 0; }
        .bullet-list li { margin-bottom: 2px; }
        .para { margin-bottom: 7px; }
        .sign-off { margin-top: 28px; font-size: 10.5px; line-height: 1.7; }
        .special-conditions { margin-top: 10px; padding: 6px 8px; border-left: 3px solid #374151; font-size: 10px; }

        /* ── ANNEXURE ────────────────────────────────────────────── */
        .annex-title { text-align: center; font-weight: 700; text-decoration: underline; margin: 10px 0 12px; font-size: 11px; text-transform: uppercase; }
        .tbl { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .tbl th, .tbl td { border: 1px solid #374151; padding: 4px 5px; font-size: 9.5px; }
        .tbl th { background: #dbeafe; font-weight: 700; text-align: center; }
        .tbl .left { text-align: left; }
        .tbl .center { text-align: center; }
        .tbl .right { text-align: right; }
        .section-ul-title { font-weight: 700; font-size: 10.5px; text-decoration: underline; margin-bottom: 4px; }

        .notes { font-size: 9.5px; margin-top: 6px; }
        .notes ul { margin: 3px 0 6px 16px; padding: 0; }
        .notes ol { margin: 3px 0 0 16px; padding: 0; }
        .notes li { margin-bottom: 2px; }
        .muted { color: #4b5563; }
    </style>
</head>
<body>
    @php
        // ── Company settings ────────────────────────────────────────
        $cs = \App\Models\CompanySetting::allKeyed();

        // ── Logo ────────────────────────────────────────────────────
        $logoAbsolutePath = \App\Models\CompanySetting::logoAbsolutePath();
        $logoDataUri = null;
        if (is_string($logoAbsolutePath) && is_file($logoAbsolutePath)) {
            $logoDataUri = 'data:image/png;base64,'.base64_encode((string) file_get_contents($logoAbsolutePath));
        }
        $companyName      = $cs['company_name']       ?? 'Overseas Marine Services';
        $companyLegalName = $cs['company_legal_name'] ?? $companyName;
        $companyAddress   = $cs['company_address']    ?? '';
        $companyPhone     = $cs['company_phone']      ?? '';
        $companyEmail     = $cs['company_email']      ?? '';
        $companyWebsite   = $cs['company_website']    ?? '';
        $signatoryName    = $cs['signatory_name']     ?? '';
        $signatoryRole    = $cs['signatory_role']     ?? '';

        // Annexure II rates
        $accomSingle      = $cs['accom_single_rate']  ?? 'xx.00';
        $accomDouble      = $cs['accom_double_rate']  ?? 'xx.00';
        $accomEvents      = $cs['accom_events_rate']  ?? 'xx.00';
        $transportRates   = [
            $cs['transport_rate_1'] ?? 'xx.00',
            $cs['transport_rate_2'] ?? 'xx.00',
            $cs['transport_rate_3'] ?? 'xx.00',
            $cs['transport_rate_4'] ?? 'xx.00',
            $cs['transport_rate_5'] ?? 'xxx.00',
        ];

        // Footer icons (SVG data URI for reliable DOMPDF rendering)
        $footerIconSvg = [
            'address' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><circle cx="8" cy="8" r="8" fill="#1e40af"/><path d="M8 3.2a2.7 2.7 0 0 0-2.7 2.7c0 2 2.7 5.9 2.7 5.9s2.7-3.9 2.7-5.9A2.7 2.7 0 0 0 8 3.2Zm0 3.6A1.1 1.1 0 1 1 8 4.6a1.1 1.1 0 0 1 0 2.2Z" fill="#fff"/></svg>',
            'phone' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><circle cx="8" cy="8" r="8" fill="#1e40af"/><path d="M5 4.3c.2-.2.5-.2.7 0l1.2 1c.2.2.2.5.1.7l-.6.9a7.5 7.5 0 0 0 2.7 2.7l.9-.6c.2-.1.5-.1.7.1l1 1.2c.2.2.2.5 0 .7l-.6.6c-.6.6-1.4.8-2.2.6a9.7 9.7 0 0 1-4.1-2.7A9.7 9.7 0 0 1 4 5.4c-.2-.8 0-1.6.6-2.2l.4-.5Z" fill="#fff"/></svg>',
            'email' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><circle cx="8" cy="8" r="8" fill="#1e40af"/><path d="M4 5.1h8a.5.5 0 0 1 .5.5v4.8a.5.5 0 0 1-.5.5H4a.5.5 0 0 1-.5-.5V5.6a.5.5 0 0 1 .5-.5Zm0 1 4 2.6 4-2.6" fill="none" stroke="#fff" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'web' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><circle cx="8" cy="8" r="8" fill="#1e40af"/><path d="M8 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm0 0c1.1 1 1.1 7 0 8m0-8c-1.1 1-1.1 7 0 8M4 8h8M4.7 6h6.6" fill="none" stroke="#fff" stroke-width="1.2" stroke-linecap="round"/></svg>',
        ];
        $footerIcons = collect($footerIconSvg)->mapWithKeys(
            fn (string $svg, string $key) => [$key => 'data:image/svg+xml;base64,'.base64_encode($svg)]
        )->all();

        // ── Quote fields ────────────────────────────────────────────
        $docNo      = str_replace('-', '/', $quote->doc_no);
        $issueDate  = optional($quote->issue_date)->format('d')
                    . '<sup>' . optional($quote->issue_date)->format('S') . '</sup> '
                    . optional($quote->issue_date)->format('M Y');
        $expiryDate = $quote->expiry_date
                    ? optional($quote->expiry_date)->format('d')
                      . '<sup>' . optional($quote->expiry_date)->format('S') . '</sup> '
                      . optional($quote->expiry_date)->format('M Y')
                    : null;
        $startDate  = optional($quote->start_date)->format('d M Y');
        $endDate    = optional($quote->end_date)->format('d M Y');

        $clientName        = $quote->client_name ?: 'Client Name';
        $location          = $quote->location ?: 'Abu Dhabi, UAE';
        $subject           = $quote->project_name ?: 'Crew Requirements';
        $scope             = $quote->scope ?: 'Source and Supply of appropriate crew who will be under Supplier payroll and seconded to Client. Rates provided as per Annex I';
        $duration          = $quote->duration_text ?: 'One (1) year';
        $paymentTerms      = $quote->payment_terms;
        $vessel            = $quote->vessel;
        $clientPo          = $quote->client_po;
        $specialConditions = $quote->special_conditions;
        $year              = optional($quote->issue_date)->year ?? now()->year;

        // ── Client address block ────────────────────────────────────
        $client          = $quote->client;
        $toContactPerson = $client?->contact_person ?: null;
        $toContactDesig  = $client?->contact_designation ?: null;
        $toCompany       = $client?->company ?: $clientName;
        $toAddress       = $client?->address ?: null;
        $toCity          = $client?->city ?: $location;

        // ── Crew line basis detection (for adaptive table headers) ──
        $lines          = $quote->crewLines;
        $isAllMonth     = $lines->isNotEmpty() && $lines->every(fn ($l) => $l->basis === 'Month');
        $hasMixedBasis  = $lines->isNotEmpty() && $lines->pluck('basis')->unique()->count() > 1;
        $rateHeader     = $isAllMonth ? "Monthly Rate Per\nPax ({$quote->currency})" : "Daily Rate Per\nPax ({$quote->currency})";
        $durationHeader = $isAllMonth ? "Duration\n(Months)" : "Duration\n(Days)";
    @endphp

    {{-- ═══════════════════ PAGE 1 ═══════════════════ --}}
    <div class="page">
        <table class="sheet-header">
            <tr>
                <td class="logo-cell">
                    @if ($logoDataUri)
                        <img src="{{ $logoDataUri }}" alt="{{ $companyName }}">
                    @else
                        <strong style="font-size:13px;color:#1e40af;">{{ $companyName }}</strong>
                    @endif
                </td>
                <td class="title-cell">Commercial Proposal for<br>Provision of Crew Consultancy Services</td>
                <td class="meta-cell">
                    <strong>Quotation No.:</strong> {{ $docNo }}<br>
                    <strong>Date:</strong> {!! $issueDate !!}
                    @if ($expiryDate)
                        <br><strong>Valid Until:</strong> {!! $expiryDate !!}
                    @endif
                    @if ($clientPo)
                        <br><strong>Client PO:</strong> {{ $clientPo }}
                    @endif
                    @if ($vessel)
                        <br><strong>Vessel/Project:</strong> {{ $vessel }}
                    @endif
                    @if ($startDate && $endDate)
                        <br><strong>Contract Period:</strong> {{ $startDate }} – {{ $endDate }}
                    @endif
                </td>
            </tr>
        </table>

        <div class="to-block">
            <strong>To:</strong><br>
            @if ($toContactPerson)
                {{ $toContactPerson }},<br>
            @endif
            @if ($toContactDesig)
                {{ $toContactDesig }},<br>
            @endif
            {{ $toCompany }},<br>
            @if ($toAddress)
                {{ $toAddress }},<br>
            @endif
            {{ $toCity }}
        </div>

        <div class="subject">Subject: {{ $subject }}</div>

        <div class="section">
            <div class="section-title">1.&nbsp; Introduction:</div>
            {{ $companyLegalName }} (hereinafter referred to as "Supplier") is pleased to
            submit this commercial proposal to {{ $clientName }} (hereinafter referred to as "Client")
            for the provision of crew supply services as outlined below.
        </div>

        <div class="section">
            <div class="section-title">2.&nbsp; Scope of Work:</div>
            {{ $scope }}
        </div>

        <div class="section">
            <div class="section-title">3.&nbsp; Definitions:</div>
            <ul class="bullet-list">
                <li><strong>Employment Contract:</strong> The letter issued by the Supplier to the Secondee (crew) setting out the terms of the secondment.</li>
                <li><strong>Employment Services:</strong> Services to be carried out by the Secondee as agreed by the parties.</li>
                <li><strong>Secondee:</strong> Supplier employee seconded to the Client under this proposal.</li>
            </ul>
        </div>

        <div class="section">
            <div class="section-title">4.&nbsp; Duration &amp; Termination:</div>
            This proposal is effective upon acceptance by the Client and will continue for firm <strong>{{ $duration }}</strong>
            from the mobilisation date
            @if ($startDate && $endDate)
                ({{ $startDate }} to {{ $endDate }})
            @endif
            ,
            with the option to extend for additional 30 days. If Client terminates
            the agreement before completing the firm period, the Supplier must be reimbursed for the balance period
            at daily rates as per Annex I. Supplier can terminate the agreement with 2 days' notice period if the
            payment as per clause 6 is not fulfilled.
        </div>

        <div class="section">
            <div class="section-title">5.&nbsp; Engagement of Secondee:</div>
            <ul class="bullet-list">
                <li>The employment contract between the Supplier and the Secondee will remain in force during the secondment period.</li>
                <li>The Supplier will ensure all necessary UAE visas, work permits, and security passes are valid throughout the contract period.</li>
            </ul>
        </div>

        <div class="section">
            <div class="section-title">6.&nbsp; Fees and Payment Terms:</div>
            @if ($paymentTerms)
                <strong>Payment Terms:</strong> {{ $paymentTerms }}<br><br>
            @endif
            Invoice for advance payment (for crew wages) issued by Supplier before 10<sup>th</sup> of every month and
            Client must release it before 20<sup>th</sup> of every month so that Supplier will pay crew before month end
            via UAE Wage Protection System. <strong>First month invoice will be raised before mobilisation and to
            be paid in advance upon signing the contract but before initial mobilisation.</strong> All rates are VAT exclusive.
        </div>

        <div class="footer-bar">
            <div class="footer-company">{{ $companyName }}.</div>
            <table class="footer-grid">
                <tr>
                    <td class="footer-col"><img class="footer-icon" src="{{ $footerIcons['address'] }}" alt=""> {{ $companyAddress }}</td>
                    <td class="footer-col"><img class="footer-icon" src="{{ $footerIcons['phone'] }}" alt=""> {{ $companyPhone }}</td>
                    <td class="footer-col"><img class="footer-icon" src="{{ $footerIcons['email'] }}" alt=""> {{ $companyEmail }}</td>
                    <td class="footer-col"><img class="footer-icon" src="{{ $footerIcons['web'] }}" alt=""> {{ $companyWebsite }}</td>
                    <td class="footer-page">Page 1 of 4</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- ═══════════════════ PAGE 2 ═══════════════════ --}}
    <div class="page">
        <table class="sheet-header">
            <tr>
                <td class="logo-cell">
                    @if ($logoDataUri)
                        <img src="{{ $logoDataUri }}" alt="{{ $companyName }}">
                    @else
                        <strong style="font-size:13px;color:#1e40af;">{{ $companyName }}</strong>
                    @endif
                </td>
                <td class="title-cell">Commercial Proposal for<br>Provision of Crew Consultancy Services</td>
                <td class="meta-cell">
                    <strong>Quotation No.:</strong> {{ $docNo }}<br>
                    <strong>Date:</strong> {!! $issueDate !!}
                </td>
            </tr>
        </table>

        <div class="section">
            <div class="section-title">7.&nbsp; Additional Services</div>
            The Supplier provides additional services as per <strong>Appendix II</strong> as and when client requires it.
        </div>

        <div class="section">
            <div class="section-title">8.&nbsp; Indemnity:</div>
            <div class="para">
                Each party shall adhere to all pertinent local and federal government regulations and statutes. The
                Supplier shall indemnify the Client against all claims arising from the employment of the secondees,
                except where termination is conducted in accordance with best practices and applicable UAE laws.
            </div>
        </div>

        <div class="section">
            <div class="section-title">9.&nbsp; Force Majeure</div>
            <div class="para">
                In view of the current regional tensions and the potential for unforeseen disruptions, including but
                not limited to airspace closures, travel restrictions, or any other circumstances beyond the
                reasonable control of the Supplier, such events shall be deemed to constitute a Force Majeure
                condition.
            </div>
            <div class="para">
                The Supplier shall not be held liable for any delay, interruption, or failure in the performance of its
                obligations resulting directly or indirectly from such Force Majeure events. Any additional costs
                incurred as a consequence &mdash; including, without limitation, extended hotel accommodation,
                standby charges, crew waiting time, or travel rescheduling due to flight cancellations or
                operational disruptions &mdash; shall be borne by the Client at actuals.
            </div>
            <div class="para">
                The Supplier shall promptly notify the Client of the occurrence of any such event and shall take all
                reasonable measures to mitigate its impact.
            </div>
        </div>

        {{-- #2 Special / Additional Conditions (only if filled on the quote) --}}
        @if ($specialConditions)
            <div class="section">
                <div class="section-title">10.&nbsp; Special Conditions:</div>
                <div class="special-conditions">{{ $specialConditions }}</div>
            </div>
        @endif

        <div class="sign-off">
            <strong>For {{ $companyName }}</strong><br>
            <br><br>
            {{ $signatoryName }}<br>
            {{ $signatoryRole }}
        </div>

        <div class="footer-bar">
            <div class="footer-company">{{ $companyName }}.</div>
            <table class="footer-grid">
                <tr>
                    <td class="footer-col"><img class="footer-icon" src="{{ $footerIcons['address'] }}" alt=""> {{ $companyAddress }}</td>
                    <td class="footer-col"><img class="footer-icon" src="{{ $footerIcons['phone'] }}" alt=""> {{ $companyPhone }}</td>
                    <td class="footer-col"><img class="footer-icon" src="{{ $footerIcons['email'] }}" alt=""> {{ $companyEmail }}</td>
                    <td class="footer-col"><img class="footer-icon" src="{{ $footerIcons['web'] }}" alt=""> {{ $companyWebsite }}</td>
                    <td class="footer-page">Page 2 of 4</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- ═══════════════════ PAGE 3 — ANNEXURE I ═══════════════════ --}}
    <div class="page">
        <table class="sheet-header">
            <tr>
                <td class="logo-cell">
                    @if ($logoDataUri)
                        <img src="{{ $logoDataUri }}" alt="{{ $companyName }}">
                    @else
                        <strong style="font-size:13px;color:#1e40af;">{{ $companyName }}</strong>
                    @endif
                </td>
                <td class="title-cell">Commercial Proposal for<br>Provision of Crew Consultancy Services</td>
                <td class="meta-cell">
                    <strong>Quotation No.:</strong> {{ $docNo }}<br>
                    <strong>Date:</strong> {!! $issueDate !!}
                </td>
            </tr>
        </table>

        <div class="annex-title">ANNEXURE I: SCHEDULE OF FEES</div>

        {{-- #4 Adaptive headers based on crew line basis --}}
        <table class="tbl">
            <thead>
                <tr>
                    <th style="width: 7%;">Sl<br>No.</th>
                    <th class="left" style="width: {{ $hasMixedBasis ? '30%' : '38%' }};">Position</th>
                    @if ($hasMixedBasis)
                        <th style="width: 9%;">Basis</th>
                    @endif
                    <th style="width: 8%;">Qty</th>
                    <th style="width: 14%;">{!! nl2br(e($durationHeader)) !!}</th>
                    <th style="width: 20%;">{!! nl2br(e($rateHeader)) !!}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lines as $line)
                    @php
                        $lineDuration = match ($line->basis) {
                            'Month' => $line->duration_months ?: ($line->duration ?: 'xxx'),
                            default => $line->duration_days   ?: ($line->duration ?: 'xxx'),
                        };
                        $lineRate = $line->basis === 'Month'
                            ? number_format((float) $line->monthly_rate, 2)
                            : number_format((float) $line->rate, 2);
                    @endphp
                    <tr>
                        <td class="center">{{ $loop->iteration }}</td>
                        <td class="left">{{ $line->rank ?: '-' }}</td>
                        @if ($hasMixedBasis)
                            <td class="center">{{ $line->basis }}</td>
                        @endif
                        <td class="center">{{ $line->qty }}</td>
                        <td class="center">{{ $lineDuration }}</td>
                        <td class="right">{{ $lineRate }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $hasMixedBasis ? 6 : 5 }}" class="center">No crew lines available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="notes">
            <strong>*The proposed lump sum {{ $isAllMonth ? 'monthly' : 'daily' }} rate includes the following:</strong>
            <ul class="bullet-list">
                <li>Crew wages</li>
                <li>ADNOC Offshore Medicals</li>
                <li>Local passes like CICPA, HSE Passport</li>
                <li>UAE Visa, Emirates ID &amp; MOHRE Labour Contract</li>
                <li>Payroll Administration via UAE WPS system</li>
                <li>Working Gear (Helmets, Safety Boots, Coveralls)</li>
                <li>UAE Health insurance and Life/Occupational Injury (LOE) insurances</li>
                <li>Administration Fees</li>
                <li>Accommodation (for one day) &amp; local transportation during mobilisation &amp; demobilisation</li>
            </ul>
            <strong>Notes:</strong>
            <ol>
                <li>The rates quoted are for firm {{ $duration }}.</li>
                <li>The crew must be included in the P&amp;I of the vessel/barge.</li>
                <li>The rates are applicable once the crew is mobilised to Abu Dhabi with all necessary passes to work in the field and charged till demobilised from Abu Dhabi.</li>
                <li>Any applicable Overtime beyond standard work hours will be back charged pro rata.</li>
                <li>Crew rotation planning and management will be client's scope of work.</li>
                <li>LOA must be issued by Client for securing CICPA, if needed by Supplier.</li>
            </ol>
        </div>

        <div class="footer-bar">
            <div class="footer-company">{{ $companyName }}.</div>
            <table class="footer-grid">
                <tr>
                    <td class="footer-col"><img class="footer-icon" src="{{ $footerIcons['address'] }}" alt=""> {{ $companyAddress }}</td>
                    <td class="footer-col"><img class="footer-icon" src="{{ $footerIcons['phone'] }}" alt=""> {{ $companyPhone }}</td>
                    <td class="footer-col"><img class="footer-icon" src="{{ $footerIcons['email'] }}" alt=""> {{ $companyEmail }}</td>
                    <td class="footer-col"><img class="footer-icon" src="{{ $footerIcons['web'] }}" alt=""> {{ $companyWebsite }}</td>
                    <td class="footer-page">Page 3 of 4</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- ═══════════════════ PAGE 4 — ANNEXURE II ═══════════════════ --}}
    <div class="page">
        <table class="sheet-header">
            <tr>
                <td class="logo-cell">
                    @if ($logoDataUri)
                        <img src="{{ $logoDataUri }}" alt="{{ $companyName }}">
                    @else
                        <strong style="font-size:13px;color:#1e40af;">{{ $companyName }}</strong>
                    @endif
                </td>
                <td class="title-cell">Commercial Proposal for<br>Provision of Crew Consultancy Services</td>
                <td class="meta-cell">
                    <strong>Quotation No.:</strong> {{ $docNo }}<br>
                    <strong>Date:</strong> {!! $issueDate !!}
                </td>
            </tr>
        </table>

        <div class="annex-title">
            ANNEXURE II: RATES FOR ADDITIONAL SERVICES<br>
            (IF APPLICABLE)
        </div>

        <div class="section-ul-title">1.&nbsp; ACCOMMODATION / HOTEL CHARGES*</div>
        <table class="tbl" style="margin-top:4px;">
            <thead>
                <tr>
                    <th class="left">Reservation type</th>
                    <th>Rates for Single Room</th>
                    <th>Rates for Double Room</th>
                    <th>Effective date</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="left">Room with Full Board Including 2 PCs Laundry Per Guest</td>
                    <td class="center">AED {{ $accomSingle }}</td>
                    <td class="center">AED {{ $accomDouble }}</td>
                    <td class="center">01<sup>st</sup> JAN {{ $year }}&ndash; 31<sup>st</sup> DEC {{ $year }}</td>
                </tr>
                <tr>
                    <td class="left">Supplementary/Additional Charges during ADIPEC, Formula 1, WEFS &amp; IDEX functions</td>
                    <td class="center" colspan="2">AED {{ $accomEvents }}</td>
                    <td class="center">As per UAE {{ $year }} Calendar</td>
                </tr>
            </tbody>
        </table>
        <div class="muted" style="font-size:9px; margin-bottom:10px;">*Standard terms and conditions apply</div>

        <div class="section-ul-title">2.&nbsp; TRANSPORTATION CHARGES</div>
        <table class="tbl" style="margin-top:4px;">
            <thead>
                <tr>
                    <th style="width:10%;">Sl. No.</th>
                    <th class="left">Trip Details</th>
                    <th style="width:22%;">Rate per Trip<br>(AED)</th>
                </tr>
            </thead>
            <tbody>
                <tr><td class="center">1</td><td class="left">Abu Dhabi City limits to Free Port (within 5 KM)</td><td class="right">{{ $transportRates[0] }}</td></tr>
                <tr><td class="center">2</td><td class="left">Abu Dhabi City to Abu Dhabi Airport/ Bateen airport</td><td class="right">{{ $transportRates[1] }}</td></tr>
                <tr><td class="center">3</td><td class="left">ADNEC / Our Hotel Area to City limits</td><td class="right">{{ $transportRates[2] }}</td></tr>
                <tr><td class="center">4</td><td class="left">Abu Dhabi City to Musaffah</td><td class="right">{{ $transportRates[3] }}</td></tr>
                <tr><td class="center">5</td><td class="left">Abu Dhabi City to Dubai Airport/Dubai City Limits</td><td class="right">{{ $transportRates[4] }}</td></tr>
            </tbody>
        </table>
        <ul class="bullet-list" style="font-size:9.5px; margin-bottom:10px;">
            <li>Sedan car with maximum 2 crew &amp; luggage and quoted one way trip. Seven or more seaters can be provided upon request at additional cost.</li>
        </ul>

        <div class="section-ul-title">3.&nbsp; FLIGHT CHARGES</div>
        <ul class="bullet-list" style="font-size:9.5px; margin-top:4px;">
            <li>We have exclusive marine and non-marine rates with various airlines and can be provided with actual + 5% service charges.</li>
        </ul>

        <div class="footer-bar">
            <div class="footer-company">{{ $companyName }}.</div>
            <table class="footer-grid">
                <tr>
                    <td class="footer-col"><img class="footer-icon" src="{{ $footerIcons['address'] }}" alt=""> {{ $companyAddress }}</td>
                    <td class="footer-col"><img class="footer-icon" src="{{ $footerIcons['phone'] }}" alt=""> {{ $companyPhone }}</td>
                    <td class="footer-col"><img class="footer-icon" src="{{ $footerIcons['email'] }}" alt=""> {{ $companyEmail }}</td>
                    <td class="footer-col"><img class="footer-icon" src="{{ $footerIcons['web'] }}" alt=""> {{ $companyWebsite }}</td>
                    <td class="footer-page">Page 4 of 4</td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
