<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice {{ $invoice->invoice_code }}</title>
    <style>
        @page {
            margin: 0px;
        }
        * {
            box-sizing: border-box;
        }

        html,
        body,
        div,
        p,
        span,
        table,
        thead,
        tbody,
        tr,
        th,
        td,
        li,
        ol,
        ul,
        strong,
        b,
        h1,
        h2,
        h3 {
            font-family: sans-serif !important;
        }

        body {
            margin: 0;
            color: #334155;
            font-size: 12px;
            line-height: 1.55;
        }

        .invoice-paper {
            background: #ffffff;
            padding: 20px 20px;
        }

        .row {
            width: 100%;
            font-size: 0;
        }

        .col-half {
            display: inline-block;
            width: 50%;
            vertical-align: top;
            font-size: 12px;
        }

        .align-right {
            text-align: right;
        }

        .brand-title {
            margin: 0;
            font-size: 34px;
            font-weight: 800;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: #0f172a;
        }

        .brand-subtitle {
            margin-top: -10px;
            color: #64748b;
            font-size: 10px;
            letter-spacing: 0.65em;
            text-transform: uppercase;
            font-weight: 600;
        }

        .divider {
            margin: 16px 0 22px;
            border-bottom: 1px solid #e2e8f0;
        }

        .section {
            margin-top: 18px;
        }

        .section-company {
            margin-bottom: 50px;
        }

        .section-billing {
            margin-top: 30px;
            margin-bottom: 50px;
        }

        .section-items {
            margin-top: 0;
            margin-bottom: 32px;
        }

        .section-payment {
            margin-top: 80px;
        }

        .section-title {
            margin: 0 0 5px;
            color: #64748b;
            text-transform: uppercase;
            font-size: 10px;
            font-weight: 700;
        }

        .value-line {
            margin: 0 0 3px;
            color: #1e293b;
        }

        .company-name {
            font-weight: 700;
            font-size:20px;
            color: #0f172a;
        }

        .invoice-number-block {
            text-align: left;
        }

        .invoice-number-label {
            margin: 0 0 4px;
            color: #64748b;
            text-transform: uppercase;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.08em;
        }

        .invoice-number-value {
            margin: 0;
            color: #0f172a;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
        }

        thead th {
            padding: 10px 11px;
            text-align: left;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.08em;
            background: #f1f5f9;
            color: #475569;
            border-bottom: 1px solid #e2e8f0;
        }

        tbody td {
            padding: 10px 11px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .number-col {
            text-align: right;
            white-space: nowrap;
        }

        .summary-box {
            margin-top: 16px;
            margin-left: auto;
            width: 265px;
        }

        .summary-table {
            margin-top: 0;
            background: #f8fafc;
        }

        .summary-table td {
            background: #f8fafc;
        }

        .summary-label {
            color: #334155;
            font-weight: 600;
        }

        .summary-value {
            text-align: right;
            color: #0f172a;
            font-weight: 700;
        }

        .payment-steps {
            margin: 8px 0 0;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background: #f8fafc;
            padding: 12px 14px;
        }

        .payment-steps ol {
            margin: 0;
            padding-left: 18px;
            color: #334155;
        }

        .payment-steps li {
            margin: 0 0 5px;
        }

        .footer-space {
            margin-top: 24px;
            border-top: 1px dashed #e2e8f0;
            padding-top: 14px;
            color: #64748b;
            font-size: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
@php
    $invoiceTotalAmount = (float) ($invoice->total_amount ?? 0);
    if ($invoiceTotalAmount <= 0) {
        $invoiceTotalAmount = (float) $invoice->lines->sum('subtotal');
    }
@endphp
<div class="invoice-paper">
    <div class="row">
        <div class="col-half">
            <img alt="Logo Nihonskuy" src="{{ public_path('logo.png') }}" style="height: 80px; width: auto;">
        </div>
        <div class="col-half align-right">
            <p class="brand-title">Invoice</p>
            <p class="brand-subtitle">Nihons Finance</p>
        </div>
    </div>

    <div class="divider"></div>

    <div class="row section section-company">
        <div class="col-half">
            <div class="invoice-number-block">
                <p class="invoice-number-value">#{{ $invoice->invoice_code }}</p>
            </div>
        </div>
        <div class="col-half align-right">
            <p class="value-line company-name">Nihonskuy</p>
            <p class="value-line">Hakuun-cho 457, Minami-ku, </br> Nagoya-shi, Aichi-ken 457-0025, Japan.</p>
        </div>
    </div>

    <div class="row section section-billing">
        <div class="col-half">
            <p class="section-title">Ditagihkan Kepada</p>
            <p class="value-line"><strong>{{ $invoice->customer?->full_name ?? '-' }}</strong></p>
            <p class="value-line">{{ $invoice->customer?->address ?? '-' }}</p>
            <p class="value-line">{{ $invoice->customer?->email ?? '-' }}</p>
        </div>
        <div class="col-half align-right">
            <p class="value-line"><strong>Invoice Date:</strong> {{ $invoice->issue_date?->format('d/m/Y') }}</p>
            <p class="value-line"><strong>Payment Due:</strong> {{ $invoice->due_date?->format('d/m/Y') }}</p>
            <p class="value-line"><strong>Amount Due:</strong> Rp {{ number_format($invoiceTotalAmount, 2, ',', '.') }}</p>
        </div>
    </div>

    <div class="section section-items">
        <table>
            <thead>
            <tr>
                <th>Items</th>
                <th class="number-col">Qty</th>
                <th class="number-col">Unit Price</th>
                <th class="number-col">Total</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($invoice->lines as $line)
                <tr>
                    <td>{{ $line->product?->title ?? '-' }}</td>
                    <td class="number-col">{{ $line->qty }}</td>
                    <td class="number-col">Rp {{ number_format((float) $line->unit_price, 2, ',', '.') }}</td>
                    <td class="number-col">Rp {{ number_format((float) $line->subtotal, 2, ',', '.') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="summary-box">
        <table class="summary-table">
            <tbody>
            <tr>
                <td class="summary-label">Total Payment</td>
                <td class="summary-value">Rp {{ number_format($invoiceTotalAmount, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="summary-label">Amount Due</td>
                <td class="summary-value">Rp {{ number_format($invoiceTotalAmount, 2, ',', '.') }}</td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="section section-payment">
        <p class="section-title">Cara Pembayaran</p>
        <div class="payment-steps">
            <ol>
                <li>Masuk ke aplikasi mobile banking atau internet banking Anda.</li>
                <li>Pilih menu transfer bank, lalu masukkan rekening tujuan invoice.</li>
                <li>Masukkan nominal sesuai jumlah Amount Due pada invoice.</li>
                <li>Isi berita transfer menggunakan kode invoice sebagai referensi pembayaran.</li>
                <li>Simpan bukti transfer dan kirimkan konfirmasi ke admin.</li>
            </ol>
        </div>
    </div>

    <div class="footer-space">
        Copyright &copy; {{ date('Y') }} Nihonskuy
    </div>
</div>
</body>
</html>
