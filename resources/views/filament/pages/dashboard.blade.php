<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

        {{-- ── STATS ROW 1 ── --}}
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">

            <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.07);">
                <p style="font-size: 0.8rem; color: #6b7280; margin-bottom: 0.25rem;">{{ __('Total Sales') }}</p>
                <p style="font-size: 1.5rem; font-weight: 700; color: #16a34a;">EGP {{ number_format($totalSalesAmount, 2) }}</p>
                <p style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem;">{{ $totalInvoices }} {{ __('invoices') }}</p>
            </div>

            <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.07);">
                <p style="font-size: 0.8rem; color: #6b7280; margin-bottom: 0.25rem;">{{ __("Today's Sales") }}</p>
                <p style="font-size: 1.5rem; font-weight: 700; color: #2563eb;">EGP {{ number_format($salesToday, 2) }}</p>
                <p style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem;">{{ $invoicesToday }} {{ __('invoices today') }}</p>
            </div>

            <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.07);">
                <p style="font-size: 0.8rem; color: #6b7280; margin-bottom: 0.25rem;">{{ __('Total Purchases') }}</p>
                <p style="font-size: 1.5rem; font-weight: 700; color: #ea580c;">EGP {{ number_format($totalPurchasesAmount, 2) }}</p>
                <p style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem;">{{ $totalPurchaseInvoices }} {{ __('purchase invoices') }}</p>
            </div>

            <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.07);">
                <p style="font-size: 0.8rem; color: #6b7280; margin-bottom: 0.25rem;">{{ __('Pending Quotations') }}</p>
                <p style="font-size: 1.5rem; font-weight: 700; color: #ca8a04;">{{ $pendingQuotations }}</p>
                <p style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem;">{{ __('out of') }} {{ $totalQuotations }} {{ __('total') }}</p>
            </div>

        </div>

        {{-- ── STATS ROW 2 ── --}}
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">

            <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.07);">
                <p style="font-size: 0.8rem; color: #6b7280; margin-bottom: 0.25rem;">{{ __('Total Books') }}</p>
                <p style="font-size: 1.5rem; font-weight: 700; color: #1f2937;">{{ $totalBooks }}</p>
            </div>

            <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.07);">
                <p style="font-size: 0.8rem; color: #6b7280; margin-bottom: 0.25rem;">{{ __('Out of Stock') }}</p>
                <p style="font-size: 1.5rem; font-weight: 700; color: #dc2626;">{{ $outOfStockBooks }}</p>
                <p style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem;">{{ __('books need restocking') }}</p>
            </div>

            <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.07);">
                <p style="font-size: 0.8rem; color: #6b7280; margin-bottom: 0.25rem;">{{ __('Total Clients') }}</p>
                <p style="font-size: 1.5rem; font-weight: 700; color: #1f2937;">{{ $totalClients }}</p>
            </div>

            <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.07);">
                <p style="font-size: 0.8rem; color: #6b7280; margin-bottom: 0.25rem;">{{ __('Total Suppliers') }}</p>
                <p style="font-size: 1.5rem; font-weight: 700; color: #1f2937;">{{ $totalSuppliers }}</p>
            </div>

        </div>

        {{-- ── BOTTOM ROW ── --}}
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">

            {{-- Recent Invoices --}}
            <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.07); overflow: hidden;">
                <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #f3f4f6; background: #f9fafb;">
                    <h3 style="font-weight: 600; color: #1f2937; font-size: 0.9rem;">{{ __('Recent Invoices') }}</h3>
                </div>
                <ul style="margin: 0; padding: 0; list-style: none;">
                    @forelse($recentInvoices as $invoice)
                    <li style="padding: 0.75rem 1.25rem; border-bottom: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="font-size: 0.85rem; font-weight: 600; color: #1f2937; margin: 0;">{{ $invoice->invoice_number }}</p>
                            <p style="font-size: 0.75rem; color: #6b7280; margin: 0;">{{ $invoice->client->name }}</p>
                        </div>
                        <div style="text-align: right;">
                            <p style="font-size: 0.85rem; font-weight: 700; color: #16a34a; margin: 0;">EGP {{ number_format($invoice->total, 2) }}</p>
                            <p style="font-size: 0.72rem; color: #9ca3af; margin: 0;">{{ $invoice->date->format('d/m/Y') }}</p>
                        </div>
                    </li>
                    @empty
                    <li style="padding: 1rem 1.25rem; font-size: 0.85rem; color: #9ca3af;">{{ __('No invoices yet') }}</li>
                    @endforelse
                </ul>
            </div>

            {{-- Recent Purchases --}}
            <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.07); overflow: hidden;">
                <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #f3f4f6; background: #f9fafb;">
                    <h3 style="font-weight: 600; color: #1f2937; font-size: 0.9rem;">{{ __('Recent Purchases') }}</h3>
                </div>
                <ul style="margin: 0; padding: 0; list-style: none;">
                    @forelse($recentPurchases as $purchase)
                    <li style="padding: 0.75rem 1.25rem; border-bottom: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="font-size: 0.85rem; font-weight: 600; color: #1f2937; margin: 0;">{{ $purchase->invoice_number }}</p>
                            <p style="font-size: 0.75rem; color: #6b7280; margin: 0;">{{ $purchase->supplier->name }}</p>
                        </div>
                        <div style="text-align: right;">
                            <p style="font-size: 0.85rem; font-weight: 700; color: #ea580c; margin: 0;">EGP {{ number_format($purchase->total, 2) }}</p>
                            <p style="font-size: 0.72rem; color: #9ca3af; margin: 0;">{{ $purchase->date->format('d/m/Y') }}</p>
                        </div>
                    </li>
                    @empty
                    <li style="padding: 1rem 1.25rem; font-size: 0.85rem; color: #9ca3af;">{{ __('No purchases yet') }}</li>
                    @endforelse
                </ul>
            </div>

            {{-- Low Stock Alert --}}
            <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.07); overflow: hidden;">
                <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #f3f4f6; background: #f9fafb; display: flex; align-items: center; gap: 0.5rem;">
                    <h3 style="font-weight: 600; color: #1f2937; font-size: 0.9rem; margin: 0;">{{ __('Low Stock Alert') }}</h3>
                    @if($lowStockBooks->count() > 0)
                        <span style="background: #fee2e2; color: #b91c1c; font-size: 0.7rem; font-weight: 700; padding: 0.1rem 0.5rem; border-radius: 9999px;">
                            {{ $lowStockBooks->count() }}
                        </span>
                    @endif
                </div>
                <ul style="margin: 0; padding: 0; list-style: none;">
                    @forelse($lowStockBooks as $book)
                    <li style="padding: 0.75rem 1.25rem; border-bottom: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="font-size: 0.85rem; font-weight: 600; color: #1f2937; margin: 0;">{{ $book->name }}</p>
                            <p style="font-size: 0.75rem; color: #6b7280; margin: 0;">{{ $book->publisher->name ?? '-' }}</p>
                        </div>
                        <span style="
                            font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 9999px;
                            background: {{ $book->current_quantity === 0 ? '#fee2e2' : '#fef9c3' }};
                            color: {{ $book->current_quantity === 0 ? '#b91c1c' : '#854d0e' }};
                        ">
                            {{ $book->current_quantity }} {{ __('left') }}
                        </span>
                    </li>
                    @empty
                    <li style="padding: 1rem 1.25rem; font-size: 0.85rem; color: #9ca3af;">{{ __('All books are well stocked') }}</li>
                    @endforelse
                </ul>
            </div>

        </div>

    </div>
</x-filament-panels::page>