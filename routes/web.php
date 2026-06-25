<?php

use Illuminate\Support\Facades\Route;
use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/quotation/{quotation}/print', function (Quotation $quotation) {
    $pdf = Pdf::loadView('pdf.quotation', compact('quotation'));
    return $pdf->stream('quotation-' . $quotation->quotation_number . '.pdf');
})->name('quotation.print');