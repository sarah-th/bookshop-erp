<?php

use Illuminate\Support\Facades\Route;
use App\Models\Quotation;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/quotation/{quotation}/print', function (Quotation $quotation) {
    $pdf = Pdf::loadView('pdf.quotation', compact('quotation'));
    return $pdf->stream('quotation-' . $quotation->quotation_number . '.pdf');
})->name('quotation.print');

Route::get('/invoice/{invoice}/print', function (Invoice $invoice) {
    $pdf = Pdf::loadView('pdf.invoice', compact('invoice'));
    return $pdf->stream('invoice-' . $invoice->invoice_number . '.pdf');
})->name('invoice.print');