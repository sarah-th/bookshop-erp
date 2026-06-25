<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Quotation Header --}}
        <x-filament::section>
            <x-slot name="heading">
                {{ __('Quotation Information') }}
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-filament::section>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ __('Quotation Number') }}
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white font-bold">
                                    {{ $record->quotation_number }}
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
                                    {{ __('Valid Until') }}
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $record->valid_until?->format('d/m/Y') ?? '-' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ __('Status') }}
                                </dt>
                                <dd class="mt-1">
                                    <x-filament::badge 
                                        :color="match($record->status->value) {
                                            'draft' => 'gray',
                                            'sent' => 'info',
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                        }"
                                    >
                                        {{ $record->status->getLabel() }}
                                    </x-filament::badge>
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

        {{-- Quotation Items Table --}}
        <x-filament::section>
            <x-slot name="heading">
                {{ __('Quotation Items') }}
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th scope="col" class="px-6 py-3">#</th>
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
                        <tr class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
                            <td class="px-6 py-4">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 font-medium">{{ $item->book->name }}</td>
                            <td class="px-6 py-4">{{ $item->book->isbn }}</td>
                            <td class="px-6 py-4 text-center">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 text-end">
                                {{ $item->book->currency ? $item->book->currency->symbol : '$' }} 
                                {{ number_format($item->unit_price, 2) }}
                            </td>
                            <td class="px-6 py-4 text-center">{{ $item->discount }}%</td>
                            <td class="px-6 py-4 text-end font-bold">
                                {{ $item->book->currency ? $item->book->currency->symbol : '$' }} 
                                {{ number_format($item->net_value, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        {{-- Totals --}}
        <x-filament::section>
            <div class="flex justify-end">
                <div class="w-full md:w-1/2 space-y-2">
                    <div class="flex justify-between py-2 border-b dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('Subtotal') }}
                        </span>
                        <span class="text-sm font-bold text-gray-900 dark:text-white">
                            EGP {{ number_format($record->subtotal, 2) }}
                        </span>
                    </div>
                    <div class="flex justify-between py-2 border-b dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('General Discount') }} ({{ $record->general_discount }}%)
                        </span>
                        <span class="text-sm font-bold text-red-600 dark:text-red-400">
                            - EGP {{ number_format($record->general_discount_amount, 2) }}
                        </span>
                    </div>
                    <div class="flex justify-between py-3 border-t-2 border-gray-900 dark:border-gray-300">
                        <span class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ __('Total') }}
                        </span>
                        <span class="text-lg font-bold text-green-600 dark:text-green-400">
                            EGP {{ number_format($record->total, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>