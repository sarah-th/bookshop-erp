<?php
// app/Filament/Pages/Dashboard.php

namespace App\Filament\Pages;

use App\Models\Book;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\PurchaseInvoice;
use App\Models\Quotation;
use App\Models\Supplier;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getView(): string
    {
        return 'filament.pages.dashboard';
    }

    public function getViewData(): array
    {
        return [
            // Sales stats
            'totalInvoices'         => Invoice::count(),
            'totalSalesAmount'      => Invoice::sum('total'),
            'invoicesToday'         => Invoice::whereDate('date', today())->count(),
            'salesToday'            => Invoice::whereDate('date', today())->sum('total'),

            // Purchase stats
            'totalPurchaseInvoices' => PurchaseInvoice::count(),
            'totalPurchasesAmount'  => PurchaseInvoice::sum('total'),

            // Quotation stats
            'totalQuotations'       => Quotation::count(),
            'pendingQuotations'     => Quotation::where('status', 'draft')->count(),

            // Stock stats
            'totalBooks'            => Book::count(),
            'outOfStockBooks'       => Book::where('current_quantity', 0)->count(),
            'lowStockBooks'         => Book::where('current_quantity', '>', 0)
                                          ->where('current_quantity', '<=', 5)
                                          ->with('publisher')
                                          ->get(),

            // Recent activity
            'recentInvoices'        => Invoice::with('client')
                                          ->latest()
                                          ->take(5)
                                          ->get(),
            'recentPurchases'       => PurchaseInvoice::with('supplier')
                                          ->latest()
                                          ->take(5)
                                          ->get(),

            // Clients & Suppliers
            'totalClients'          => Client::count(),
            'totalSuppliers'        => Supplier::count(),
        ];
    }
}