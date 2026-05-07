<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $quote->doc_no }} Proposal</title>
    <style>
        @page { size: A4; margin: 20mm 14mm 20mm 14mm; }
        body { margin: 0; font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 11px; line-height: 1.35; }
        .page { min-height: 257mm; position: relative; page-break-after: always; }
        .page:last-child { page-break-after: auto; }

        .sheet-header { border: 1px solid #6b7280; width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .sheet-header td { border: 1px solid #6b7280; padding: 6px 8px; vertical-align: middle; }
        .logo-cell { width: 22%; text-align: center; }
        .logo-mark { font-size: 15px; font-weight: 700; color: #0e7490; line-height: 1; }
        .logo-sub { font-size: 9px; color: #0e7490; margin-top: 2px; }
        .title-cell { width: 43%; text-align: center; font-size: 12px; font-weight: 700; }
        .meta-cell { width: 35%; font-size: 10px; }

        .footer-bar {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            border-top: 1px solid #94a3b8;
            padding-top: 4px;
            font-size: 8px;
            color: #374151;
        }
        .footer-grid { width: 100%; border-collapse: collapse; }
        .footer-grid td { padding: 0 3px; vertical-align: middle; }
        .footer-page { text-align: right; width: 14%; }
        .dot { color: #0369a1; font-weight: 700; }

        .to-block { margin: 10px 0 10px; font-size: 10px; line-height: 1.45; }
        .subject { margin: 8px 0 10px; font-weight: 700; font-size: 10px; }
        .section { margin-bottom: 7px; font-size: 10px; }
        .section-title { font-weight: 700; margin-bottom: 2px; }
        .bullet-list { margin: 2px 0 4px 14px; padding: 0; }
        .bullet-list li { margin-bottom: 1px; }
        .muted { color: #4b5563; }
        .sign-off { margin-top: 18px; }

        .annex-title { text-align: center; font-weight: 700; text-decoration: underline; margin: 8px 0 10px; font-size: 11px; }
        .tbl { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .tbl th, .tbl td { border: 1px solid #6b7280; padding: 4px 5px; font-size: 9.5px; }
        .tbl th { background: #e5edf8; font-weight: 700; text-align: center; }
        .tbl .left { text-align: left; }
        .tbl .center { text-align: center; }
        .tbl .right { text-align: right; }

        .notes { font-size: 9px; margin-top: 6px; }
        .notes ol { margin: 3px 0 0 14px; padding: 0; }
    </style>
</head>
<body>
    @php
        $logoAbsolutePath = storage_path('app/public/overseas-marine logo.png');
        $logoDataUri = null;
        if (is_file($logoAbsolutePath)) {
            $logoDataUri = 'data:image/png;base64,'.base64_encode((string) file_get_contents($logoAbsolutePath));
        }
    @endphp

    {{-- PAGE 1 --}}
    <div class="page">
        <table class="sheet-header">
            <tr>
                <td class="logo-cell">
                    @if ($logoDataUri)
                        <img src="{{ $logoDataUri }}" alt="Overseas Marine Services logo" style="height: 34px; width: auto;">
                    @else
                        <div class="logo-mark">Overseas</div>
                        <div class="logo-sub">Marine Services</div>
                    @endif
                </td>
                <td class="title-cell">Commercial Proposal for<br>Provision of Crew Consultancy Services</td>
                <td class="meta-cell">
                    <strong>Quotation No.:</strong> {{ str_replace('-', '/', $quote->doc_no) }}<br>
                    <strong>Date:</strong> {{ $today->format('dS M Y') }}
                </td>
            </tr>
        </table>

        <div class="to-block">
            <strong>To:</strong><br>
            {{ $quote->client_name ?: 'Client Name' }},<br>
            Crewing Supervisor,<br>
            {{ $quote->client_name ?: 'Client Company' }},<br>
            {{ $quote->location ?: 'Abu Dhabi, UAE' }}
        </div>

        <div class="subject">Subject: {{ $quote->project_name ?: 'Melody Crew Requirements - Long term' }}</div>

        <div class="section">
            <div class="section-title">1. Introduction:</div>
            Overseas Marine Services-Sole Proprietorship LLC (hereinafter referred to as "Supplier") is pleased to
            submit this commercial proposal to {{ $quote->client_name ?: 'Client' }} (hereinafter referred to as "Client")
            for the provision of crew supply services as outlined below.
        </div>

        <div class="section">
            <div class="section-title">2. Scope of Work:</div>
            {{ $quote->scope ?: 'Source and Supply of appropriate crew who will be under Supplier payroll and seconded to Client. Rates provided as per Annex I.' }}
        </div>

        <div class="section">
            <div class="section-title">3. Definitions:</div>
            <ul class="bullet-list">
                <li><strong>Employment Contract:</strong> The letter issued by the Supplier to the Secondee (crew) setting out terms of the secondment.</li>
                <li><strong>Employment Services:</strong> Services to be carried out by the Secondee as agreed by the parties.</li>
                <li><strong>Secondee:</strong> Supplier employee seconded to the Client under this proposal.</li>
            </ul>
        </div>

        <div class="section">
            <div class="section-title">4. Duration &amp; Termination:</div>
            This proposal is effective upon acceptance and will continue for firm <strong>One (1) year</strong> from the mobilisation date.
            If Client terminates before completing the firm period, balance period charges apply as per Annex I.
        </div>

        <div class="section">
            <div class="section-title">5. Engagement of Secondee:</div>
            <ul class="bullet-list">
                <li>Employment contract between Supplier and Secondee remains in force during secondment.</li>
                <li>Supplier ensures valid UAE visas, work permits and security passes through contract period.</li>
            </ul>
        </div>

        <div class="section">
            <div class="section-title">6. Fees and Payment Terms:</div>
            <ul class="bullet-list">
                <li>Invoice for advance payment issued before 10th of every month and settled before 20th.</li>
                <li>First month invoice raised before mobilisation and paid in advance before initial mobilisation.</li>
                <li>All rates are VAT exclusive.</li>
            </ul>
        </div>

        <div class="footer-bar">
            <table class="footer-grid">
                <tr>
                    <td>Overseas Marine Services, Office 402, Centro Capital Centre Offices Building, Al Zumurrud St, ADNEC Area, Abu Dhabi</td>
                    <td><span class="dot">●</span> +971 2 6714722</td>
                    <td><span class="dot">●</span> info@overseas-ms.com</td>
                    <td><span class="dot">●</span> www.overseas-ms.com</td>
                    <td class="footer-page">Page 1 of 4</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- PAGE 2 --}}
    <div class="page">
        <table class="sheet-header">
            <tr>
                <td class="logo-cell">
                    @if ($logoDataUri)
                        <img src="{{ $logoDataUri }}" alt="Overseas Marine Services logo" style="height: 34px; width: auto;">
                    @else
                        <div class="logo-mark">Overseas</div>
                        <div class="logo-sub">Marine Services</div>
                    @endif
                </td>
                <td class="title-cell">Commercial Proposal for<br>Provision of Crew Consultancy Services</td>
                <td class="meta-cell">
                    <strong>Quotation No.:</strong> {{ str_replace('-', '/', $quote->doc_no) }}<br>
                    <strong>Date:</strong> {{ $today->format('dS M Y') }}
                </td>
            </tr>
        </table>

        <div class="section">
            <div class="section-title">7. Additional Services</div>
            The Supplier provides additional services as per Appendix II as and when client requires it.
        </div>

        <div class="section">
            <div class="section-title">8. Indemnity:</div>
            Each party shall adhere to all pertinent local and federal government regulations and statutes.
            The Supplier shall indemnify the Client against all claims arising from the employment of the secondees,
            except where termination is conducted in accordance with best practices and applicable UAE laws.
        </div>

        <div class="section">
            <div class="section-title">9. Force Majeure</div>
            In view of the current regional tensions and potential unforeseen disruptions, including travel restrictions,
            such events shall be deemed force majeure conditions.
            Additional costs (hotel extension, standby, flight rescheduling, waiting time) will be borne by Client at actuals.
        </div>

        <div class="sign-off">
            <strong>For Overseas Marine Services</strong><br><br>
            Kiron V.<br>
            Commercial Manager
        </div>

        <div class="footer-bar">
            <table class="footer-grid">
                <tr>
                    <td>Overseas Marine Services, Office 402, Centro Capital Centre Offices Building, Al Zumurrud St, ADNEC Area, Abu Dhabi</td>
                    <td><span class="dot">●</span> +971 2 6714722</td>
                    <td><span class="dot">●</span> info@overseas-ms.com</td>
                    <td><span class="dot">●</span> www.overseas-ms.com</td>
                    <td class="footer-page">Page 2 of 4</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- PAGE 3 --}}
    <div class="page">
        <table class="sheet-header">
            <tr>
                <td class="logo-cell">
                    @if ($logoDataUri)
                        <img src="{{ $logoDataUri }}" alt="Overseas Marine Services logo" style="height: 34px; width: auto;">
                    @else
                        <div class="logo-mark">Overseas</div>
                        <div class="logo-sub">Marine Services</div>
                    @endif
                </td>
                <td class="title-cell">Commercial Proposal for<br>Provision of Crew Consultancy Services</td>
                <td class="meta-cell">
                    <strong>Quotation No.:</strong> {{ str_replace('-', '/', $quote->doc_no) }}<br>
                    <strong>Date:</strong> {{ $today->format('dS M Y') }}
                </td>
            </tr>
        </table>

        <div class="annex-title">ANNEXURE I: SCHEDULE OF FEES</div>
        <table class="tbl">
            <thead>
                <tr>
                    <th style="width: 7%;">Sl No.</th>
                    <th class="left" style="width: 30%;">Position</th>
                    <th style="width: 8%;">Qty</th>
                    <th style="width: 12%;">Duration (Days)</th>
                    <th style="width: 18%;">Daily Rate Per Pax</th>
                    <th style="width: 20%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quote->crewLines as $line)
                    <tr>
                        <td class="center">{{ $loop->iteration }}</td>
                        <td class="left">{{ $line->rank ?: '-' }}</td>
                        <td class="center">{{ $line->qty }}</td>
                        <td class="center">{{ $line->duration_days ?: $line->duration }}</td>
                        <td class="right">{{ $quote->currency }} {{ number_format((float) $line->rate, 2) }}</td>
                        <td class="right">{{ $quote->currency }} {{ number_format((float) $line->line_total, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="center">No crew lines available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="notes">
            <strong>*The proposed lump sum daily rate includes the following:</strong>
            <ul class="bullet-list">
                <li>Crew wages</li>
                <li>Medicals and local operational passes</li>
                <li>UAE visa / Emirates ID / labour contract support</li>
                <li>Payroll administration and HSE compliance support</li>
                <li>Working gear and relevant insurances</li>
                <li>Mobilisation / demobilisation local support</li>
            </ul>
            <strong>Notes:</strong>
            <ol>
                <li>Rates are quoted for firm period and applicable upon mobilisation readiness.</li>
                <li>Crew must be included under vessel/barge operational insurance where applicable.</li>
                <li>Overtime beyond standard work hours may be charged pro-rata.</li>
            </ol>
        </div>

        <div class="footer-bar">
            <table class="footer-grid">
                <tr>
                    <td>Overseas Marine Services, Office 402, Centro Capital Centre Offices Building, Al Zumurrud St, ADNEC Area, Abu Dhabi</td>
                    <td><span class="dot">●</span> +971 2 6714722</td>
                    <td><span class="dot">●</span> info@overseas-ms.com</td>
                    <td><span class="dot">●</span> www.overseas-ms.com</td>
                    <td class="footer-page">Page 3 of 4</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- PAGE 4 --}}
    <div class="page">
        <table class="sheet-header">
            <tr>
                <td class="logo-cell">
                    @if ($logoDataUri)
                        <img src="{{ $logoDataUri }}" alt="Overseas Marine Services logo" style="height: 34px; width: auto;">
                    @else
                        <div class="logo-mark">Overseas</div>
                        <div class="logo-sub">Marine Services</div>
                    @endif
                </td>
                <td class="title-cell">Commercial Proposal for<br>Provision of Crew Consultancy Services</td>
                <td class="meta-cell">
                    <strong>Quotation No.:</strong> {{ str_replace('-', '/', $quote->doc_no) }}<br>
                    <strong>Date:</strong> {{ $today->format('dS M Y') }}
                </td>
            </tr>
        </table>

        <div class="annex-title">ANNEXURE II: RATES FOR ADDITIONAL SERVICES<br>(IF APPLICABLE)</div>

        <div class="section-title">1. ACCOMMODATION / HOTEL CHARGES*</div>
        <table class="tbl">
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
                    <td class="left">Room with Full Board including 2 pcs laundry per guest</td>
                    <td class="center">AED xx.00</td>
                    <td class="center">AED xx.00</td>
                    <td class="center">01 Jan {{ now()->year }} - 31 Dec {{ now()->year }}</td>
                </tr>
                <tr>
                    <td class="left">Supplementary charges during special events</td>
                    <td class="center" colspan="2">AED xx.00</td>
                    <td class="center">As per UAE {{ now()->year }} Calendar</td>
                </tr>
            </tbody>
        </table>

        <div class="muted" style="font-size:9px; margin-bottom:8px;">*Standard terms and conditions apply</div>

        <div class="section-title">2. TRANSPORTATION CHARGES</div>
        <table class="tbl">
            <thead>
                <tr>
                    <th style="width:10%;">Sl. No.</th>
                    <th class="left">Trip Details</th>
                    <th style="width:20%;">Rate per Trip (AED)</th>
                </tr>
            </thead>
            <tbody>
                <tr><td class="center">1</td><td class="left">Abu Dhabi City limits to Free Port (within 5 KM)</td><td class="right">xx.00</td></tr>
                <tr><td class="center">2</td><td class="left">Abu Dhabi City to Abu Dhabi Airport / Bateen Airport</td><td class="right">xx.00</td></tr>
                <tr><td class="center">3</td><td class="left">ADNEC / Hotel Area to City limits</td><td class="right">xx.00</td></tr>
                <tr><td class="center">4</td><td class="left">Abu Dhabi City to Musaffah</td><td class="right">xx.00</td></tr>
                <tr><td class="center">5</td><td class="left">Abu Dhabi City to Dubai Airport / Dubai City Limits</td><td class="right">xxx.00</td></tr>
            </tbody>
        </table>

        <div class="section-title">3. FLIGHT CHARGES</div>
        <div class="section">Exclusive marine and non-marine airline rates can be provided at actuals + 5% service charges.</div>

        <div class="footer-bar">
            <table class="footer-grid">
                <tr>
                    <td>Overseas Marine Services, Office 402, Centro Capital Centre Offices Building, Al Zumurrud St, ADNEC Area, Abu Dhabi</td>
                    <td><span class="dot">●</span> +971 2 6714722</td>
                    <td><span class="dot">●</span> info@overseas-ms.com</td>
                    <td><span class="dot">●</span> www.overseas-ms.com</td>
                    <td class="footer-page">Page 4 of 4</td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
