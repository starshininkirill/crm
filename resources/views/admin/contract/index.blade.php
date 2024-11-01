@extends('admin.layouts.contract')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">Договоры</h1>
    <div class="contracts">
        @if ($contracts->isEmpty())
            <h2>Договоров не найдено</h2>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300">
                    <thead>
                        <tr class="bg-gray-800">
                            <th class="border border-gray-300 px-2 py-1 text-left text-white">Дата</th>
                            <th class="border border-gray-300 px-2 py-1 text-left text-white">Сотрудник</th>
                            <th class="border border-gray-300 px-2 py-1 text-left text-white">№</th>
                            <th class="border border-gray-300 px-2 py-1 text-left text-white">Компания</th>
                            <th class="border border-gray-300 px-2 py-1 text-left text-white">Номер телефона</th>
                            <th class="border border-gray-300 px-2 py-1 text-left text-white w-64">Услуги</th>
                            <th class="border border-gray-300 px-2 py-1 text-left text-white">Общая стоимость</th>
                            <th class="border border-gray-300 px-2 py-1 text-left text-white whitespace-nowrap">1-й</th>
                            <th class="border border-gray-300 px-2 py-1 text-left text-white whitespace-nowrap">2-й</th>
                            <th class="border border-gray-300 px-2 py-1 text-left text-white whitespace-nowrap">3-й</th>
                            <th class="border border-gray-300 px-2 py-1 text-left text-white whitespace-nowrap">4-й</th>
                            <th class="border border-gray-300 px-2 py-1 text-left text-white whitespace-nowrap">5-й</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contracts as $contract)
                            <tr>
                                <td class="border border-gray-300 px-2 py-1">{{ $contract->created_at->format('d.m.y') }}
                                </td>
                                <td class="border border-gray-300 px-2 py-1">
                                    @if ($contract->saller())
                                        {{ $contract->saller()->first_name }} {{ $contract->saller()->last_name }}
                                    @else
                                        Не прикреплён
                                    @endif
                                </td>
                                <td class="border border-gray-300 px-2 py-1 text-blue-700">
                                    <a href="{{ route('admin.contract.show', $contract->id) }}">
                                        {{ $contract->number }}
                                    </a>
                                </td>
                                <td class="border border-gray-300 px-2 py-1">
                                    {{ $contract->client->company }}
                                </td>
                                <td class="border border-gray-300 px-2 py-1">
                                    {{ $contract->client->phone }}
                                </td>
                                <td class="border border-gray-300 px-2 py-1">
                                    @foreach ($contract->services as $service)
                                        {{ $service->name }}
                                        @if (!$loop->last)
                                            ,
                                        @endif
                                    @endforeach
                                </td>
                                <td class="border border-gray-300 px-2 py-1">
                                    {{ $contract->getPrice() }}
                                </td>
                                @foreach ($contract->payments as $payment)
                                    <td
                                        class="border border-gray-300 px-2 py-1 whitespace-nowrap {{ $payment->status == $paymentClass::STATUS_CLOSE ? 'bg-green-500 text-white' : '' }}">
                                        <a href="{{ route('admin.payment.show', $payment->id) }}">
                                            {{ $payment->getFormatValue() }}
                                        </a>
                                    </td>
                                @endforeach

                                @for ($i = count($contract->payments); $i < 5; $i++)
                                    <td class="border border-gray-300 px-2 py-1"></td>
                                @endfor
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
