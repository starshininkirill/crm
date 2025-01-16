@extends('admin.layouts.payment')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">Платежи</h1>
    <div class="">
        @if ($payments->isEmpty())
            <h2>Платежей не найдено</h2>
        @else
            <table class="min-w-full border border-gray-300">
                <thead>
                    <tr class="bg-gray-800">
                        <th class="border border-gray-300 px-4 py-2 text-left text-white">Номер</th>
                        <th class="border border-gray-300 px-4 py-2 text-left text-white">Дата</th>
                        <th class="border border-gray-300 px-4 py-2 text-left text-white">Договор</th>
                        <th class="border border-gray-300 px-4 py-2 text-left text-white">Сумма</th>
                        <th class="border border-gray-300 px-4 py-2 text-left text-white">ИНН</th>
                        <th class="border border-gray-300 px-4 py-2 text-left text-white">Статус</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $payment)
                        <tr class="">
                            <td class="border border-gray-300 px-4 py-2">
                                <a href="{{ route('admin.payment.show', $payment->id) }}" class=" text-blue-700 underline">
                                    № {{ $payment->id }}
                                </a>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ $payment->created_at->format('H:i d.m.Y') }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                @if ($payment->contract)
                                    <a href="{{ route('admin.contract.show', $payment->contract->id) }}"
                                        class="text-blue-700">
                                        {{ $payment->contract->number }}
                                    </a>
                                @endif
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ $payment->getFormatValue() }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                @if ($payment->contract->client)
                                    {{ $payment->contract->client->inn }}
                                @endif
                            </td>
                            <td
                                class="border border-gray-300 px-4 py-2 {{ $payment->status == $paymentClass::STATUS_CLOSE ? 'bg-green-500 text-white' : '' }}">
                                {{ $payment->getStatusNameAttribute() }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
