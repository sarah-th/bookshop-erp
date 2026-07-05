<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Invoice Header --}}
        <x-filament::section>
            <x-slot name="heading">
                {{ __('Invoice Information') }}
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-filament::section>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ __('Invoice Number') }}
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white font-bold">
                                    {{ $record->invoice_number }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ __('Client') }}
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $record->client->name }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ __('Type') }}
                                </dt>
                                <dd class="mt-1">
                                    <x-filament::badge :color="$record->client->type->value === 'company' ? 'primary' : 'success'">
                                        {{ $record->client->type->getLabel() }}
                                    </x-filament::badge>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ __('Email') }}
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $record->client->email }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ __('Phone') }}
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $record->client->phone }}
                                </dd>
                            </div>
                            @if($record->quotation)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ __('From Quotation') }}
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white font-medium">
                                    {{ $record->quotation->quotation_number }}
                                </dd>
                            </div>
                            @endif
                        </dl>
                    </x-filament::section>
                </div>

                <div>
                    <x-filament::section>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ __('Date') }}
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $record->date->format('d/m/Y') }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ __('Due Date') }}
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $record->due_date?->format('d/m/Y') ?? '-' }}
                                </dd>
                            </div>
                            @if($record->notes)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ __('Notes') }}
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $record->notes }}
                                </dd>
                            </div>
                            @endif
                        </dl>
                    </x-filament::section>
                </div>
            </div>
        </x-filament::section>

        {{-- Invoice Items Table --}}
        <x-filament::section>
            <x-slot name="heading">
                {{ __('Invoice Items') }}
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th scope="col" class="px-6 py-3">#</th>
                            <th scope="col" class="px-6 py-3">{{ __('Publisher') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Book') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('ISBN') }}</th>
                            <th scope="col" class="px-6 py-3 text-center">{{ __('Quantity') }}</th>
                            <th scope="col" class="px-6 py-3 text-end">{{ __('Unit Price') }}</th>
                            <th scope="col" class="px-6 py-3 text-center">{{ __('Discount %') }}</th>
                            <th scope="col" class="px-6 py-3 text-end">{{ __('Net Value') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($record->items as $index => $item)
                        @php $currency = $item->book->currency; @endphp
                        <tr class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
                            <td class="px-6 py-4">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">{{ $item->book->publisher->name ?? '-' }}</td>
                            <td class="px-6 py-4 font-medium">{{ $item->book->name }}</td>
                            <td class="px-6 py-4">{{ $item->book->isbn }}</td>
                            <td class="px-6 py-4 text-center">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 text-end">
                                {{ $currency?->symbol ?? '$' }}
                                {{ number_format($item->unit_price, 2) }}
                            </td>
                            <td class="px-6 py-4 text-center">{{ $item->discount }}%</td>
                            <td class="px-6 py-4 text-end font-bold">
                                {{ $currency?->symbol ?? '$' }}
                                {{ number_format($item->net_value, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50 dark:bg-gray-800 font-semibold">
                            <td colspan="4" class="px-6 py-3"></td>
                            <td class="px-6 py-3 text-center">
                                {{ $record->items->sum('quantity') }}
                            </td>
                            <td colspan="3" class="px-6 py-3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-filament::section>

        {{-- Totals --}}
        <x-filament::section>
            @php
                $currency = $record->items->first()?->book?->currency;
                $currencySymbol = $currency?->symbol ?? 'EGP';
                $currencyLabel  = $currency ? "{$currency->name} ({$currency->code})" : 'EGP';
            @endphp

            <div class="flex justify-end">
                <div class="w-full md:w-1/2 space-y-2">
                    <div class="flex justify-between py-2 border-b dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('Currency') }}
                        </span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $currencyLabel }}
                        </span>
                    </div>
                    <div class="flex justify-between py-2 border-b dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('Subtotal') }}
                        </span>
                        <span class="text-sm font-bold text-gray-900 dark:text-white">
                            {{ $currencySymbol }} {{ number_format($record->subtotal, 2) }}
                        </span>
                    </div>
                    @if($record->general_discount > 0)
                    <div class="flex justify-between py-2 border-b dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('General Discount') }} ({{ $record->general_discount }}%)
                        </span>
                        <span class="text-sm font-bold text-red-600 dark:text-red-400">
                            - {{ $currencySymbol }} {{ number_format($record->general_discount_amount, 2) }}
                        </span>
                    </div>
                    @endif
                    <div class="flex justify-between py-3 border-t-2 border-gray-900 dark:border-gray-300">
                        <span class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ __('Total') }}
                        </span>
                        <span class="text-lg font-bold text-green-600 dark:text-green-400">
                            {{ $currencySymbol }} {{ number_format($record->total, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>