<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            margin: 40px 50px 70px 50px;
        }


        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #222;
            line-height: 1.5;
            padding: 20px; /* ← Add body padding */
        }

        /* ── WRAPPER ── */
        .page-wrapper {
            padding: 20px;
            border: 1px solid #e0e0e0; /* ← Optional: adds a subtle border */
            border-radius: 4px;
        }

        /* ── HEADER ── */
        .header {
            width: 100%;
            margin-bottom: 20px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo-cell {
            width: 20%;
            vertical-align: middle;
        }

        .logo-cell img {
            width: 90px;
        }

        .logo-placeholder {
            width: 90px;
            height: 70px;
            border: 2px solid #333;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 5px;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            line-height: 1.4;
        }

        .title-cell {
            width: 40%;
            text-align: center;
            vertical-align: middle;
        }

        .quotation-title-box {
            display: inline-block;
        }

        .quotation-title-box table {
            border-collapse: collapse;
            margin: 0 auto;
        }

        .quotation-title-box td {
            padding: 8px 20px;
            border: 2px solid #000;
            font-size: 16px;
            font-weight: bold;
            vertical-align: middle;
        }

        .quotation-number-box {
            background: #fff;
            font-size: 16px;
        }

        .company-name-cell {
            width: 40%;
            text-align: right;
            vertical-align: middle;
        }

        .company-name-ar {
            font-size: 20px;
            font-weight: bold;
            direction: rtl;
        }

        /* ── ADDRESS SECTION ── */
        .address-section {
            width: 100%;
            margin: 15px 0;
        }

        .address-table {
            width: 100%;
            border-collapse: collapse;
        }

        .address-col {
            width: 40%;
            vertical-align: top;
            padding-right: 15px;
        }

        .address-col h4 {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 5px;
            text-decoration: underline;
        }

        .address-col p {
            font-size: 10px;
            margin-bottom: 2px;
        }

        .meta-col {
            width: 20%;
            vertical-align: top;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #999;
        }

        .meta-table td {
            border: 1px solid #999;
            padding: 4px 8px;
            font-size: 10px;
        }

        .meta-label {
            font-weight: bold;
            background: #f0f0f0;
            white-space: nowrap;
        }

        /* ── ITEMS TABLE ── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .items-table thead tr {
            background: #d9d9d9;
        }

        .items-table th {
            border: 1px solid #999;
            padding: 8px 6px;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
        }

        .items-table td {
            border: 1px solid #ccc;
            padding: 7px 6px;
            font-size: 10px;
            vertical-align: middle;
        }

        .text-center { text-align: center; }
        .text-right  { text-align: right;  }
        .text-left   { text-align: left;   }

        .totals-row td {
            font-weight: bold;
            background: #f0f0f0;
        }

        /* ── FOOTER TABLE ── */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            border: 1px solid #999;
        }

        .footer-table td {
            border: 1px solid #999;
            padding: 8px 10px;
            vertical-align: top;
            font-size: 10px;
        }

        .footer-notes {
            width: 60%;
        }

        .footer-sign {
            width: 40%;
        }

        .footer-sign-row {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ccc;
        }

        /* ── PAGE FOOTER ── */
        .page-footer {
            position: fixed;
            bottom: 0;        /* ← was -60px */
            left: 0;
            right: 0;
            height: 60px;
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 9px;
            color: #666;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    {{-- Page Footer (fixed) --}}
    <div class="page-footer">
        Future Book Center &nbsp;|&nbsp;
        Email: info@futurebookcenter.com &nbsp;|&nbsp;
        Phone: +20 123 456 7890 &nbsp;|&nbsp;
        www.futurebookcenter.com
    </div>

    <div class="page-wrapper">

        {{-- ══ HEADER ══ --}}
        <table class="header-table">
            <tr>
                {{-- Logo --}}
                <td class="logo-cell">
                    <div class="logo-placeholder">
                        FCB<br>Future<br>Book<br>Center
                    </div>
                </td>

                {{-- Quotation Title --}}
                <td class="title-cell">
                    <div class="quotation-title-box">
                        <table>
                            <tr>
                                <td style="font-size:18px; font-weight:bold;">Invoice</td>
                                <td class="quotation-number-box">{{ $invoice->invoice_number }}</td>
                            </tr>
                        </table>
                    </div>
                </td>

                {{-- Company Arabic Name --}}
                <td class="company-name-cell">
                    <!-- <div class="company-name-ar">المكتبــة الدوليــة للغـات</div> -->
                    <div style="text-align: center;">
                        <img src="{{ public_path('images/FBC_logo_darkblue.png') }}" style="width: 120px;">
                    </div>
                </td>
            </tr>
        </table>

        <hr style="border: 1px solid #aaa; margin: 10px 0;">

        {{-- ══ ADDRESS & META ══ --}}
        <table class="address-table">
            <tr>
                {{-- Billing Address --}}
                <td class="address-col">
                    <h4>BILLING ADDRESS</h4>
                    <p style="font-weight:bold;">{{ $invoice->client->name }}</p>
                    <p>{{ $invoice->client->address }}</p>
                    <p>{{ $invoice->client->phone }}</p>
                    <p>{{ $invoice->client->email }}</p>
                </td>

                {{-- Shipping Address (same as billing) --}}
                <td class="address-col">
                    <h4>SHIPPING ADDRESS</h4>
                    <p style="font-weight:bold;">{{ $invoice->client->name }}</p>
                    <p>{{ $invoice->client->address }}</p>
                    <p>{{ $invoice->client->phone }}</p>
                    <p>{{ $invoice->client->email }}</p>
                </td>

                {{-- Meta Info --}}
                <td class="meta-col">
                    <table class="meta-table">
                        <tr>
                            <td class="meta-label">Date:</td>
                            <td>{{ $invoice->date->format('d/m/Y') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- ══ ITEMS TABLE ══ --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:18%;">Publisher</th>
                    <th style="width:15%;">ISBN</th>
                    <th style="width:32%;">Title</th>
                    <th style="width:13%;">Customer Ref.</th>
                    <th style="width:5%;">QTY</th>
                    <th style="width:8%;">Unit Price</th>
                    <th style="width:5%;">Disc. %</th>
                    <th style="width:9%;">Net Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->book->publisher->name ?? '-' }}</td>
                    <td class="text-center">{{ $item->book->isbn }}</td>
                    <td>{{ $item->book->name }}</td>
                    <td class="text-center">{{ $invoice->client->name }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-center">{{ $item->discount }}</td>
                    <td class="text-right">{{ number_format($item->net_value, 2) }}</td>
                </tr>
                @endforeach

                {{-- Totals Row --}}
                <tr class="totals-row">
                    <td colspan="3"></td>
                    <td class="text-center">Total Books:</td>
                    <td class="text-center">{{ $invoice->items->sum('quantity') }}</td>
                    <td></td>
                    <td class="text-right">Total:</td>
                    <td class="text-right">{{ number_format($invoice->total, 2) }}</td>
                </tr>

                @if($invoice->general_discount > 0)
                <tr class="totals-row">
                    <td colspan="5"></td>
                    <td colspan="2" class="text-right">General Discount ({{ $invoice->general_discount }}%):</td>
                    <td class="text-right">- {{ number_format($invoice->general_discount_amount, 2) }}</td>
                </tr>
                <tr class="totals-row">
                    <td colspan="5"></td>
                    <td colspan="2" class="text-right">Net Total:</td>
                    <td class="text-right" style="font-size:12px;">
                        {{ number_format($invoice->total, 2) }}
                    </td>
                </tr>
                @endif
            </tbody>
        </table>

        {{-- ══ FOOTER TABLE ══ --}}
        <table class="footer-table">
            <tr>
                {{-- Notes --}}
                <td class="footer-notes">
                    <p>Delivery Period: 6-8 weeks from receiving the stamped order</p>
                    <p>Validity period: 15 days from: {{ $invoice->date->format('d/m/Y') }}</p>
                    @if($invoice->notes)
                        <p style="margin-top:5px;">{{ $invoice->notes }}</p>
                    @endif
                    <p>Payment Terms: 50% in advance, 25% after one month from order date, 25% on delivery</p>
                    <p>Prices may change without prior notice</p>
                </td>

                {{-- Signature --}}
                <td class="footer-sign">
                    <div class="footer-sign-row">
                        <strong>Received By:</strong>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </div>
                    <div>
                        <strong>Signature:</strong>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>