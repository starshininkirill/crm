@extends('admin.layouts.contract')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">Договора</h1>
    <div class="contracts">
        @if ($contracts->isEmpty())
            <h2>Договоров не найдено</h2>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300">
                    <thead>
                        <tr class="bg-gray-800">
                            <th class="border border-gray-300 px-4 py-2 text-left text-white">Дата</th>
                            <th class="border border-gray-300 px-4 py-2 text-left text-white">Сотрудник</th>
                            <th class="border border-gray-300 px-4 py-2 text-left text-white">№</th>
                            <th class="border border-gray-300 px-4 py-2 text-left text-white">Имя</th>
                            <th class="border border-gray-300 px-4 py-2 text-left text-white">Компания</th>
                            <th class="border border-gray-300 px-4 py-2 text-left text-white">Номер телефона</th>
                            <th class="border border-gray-300 px-4 py-2 text-left text-white">Услуги</th>
                            <th class="border border-gray-300 px-4 py-2 text-left text-white">Общая стоимость</th>
                            <th class="border border-gray-300 px-4 py-2 text-left text-white whitespace-nowrap">1-й</th>
                            <th class="border border-gray-300 px-4 py-2 text-left text-white whitespace-nowrap">2-й</th>
                            <th class="border border-gray-300 px-4 py-2 text-left text-white whitespace-nowrap">3-й</th>
                            <th class="border border-gray-300 px-4 py-2 text-left text-white whitespace-nowrap">4-й</th>
                            <th class="border border-gray-300 px-4 py-2 text-left text-white whitespace-nowrap">5-й</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contracts as $contract)
                            <tr>
                                <td class="border border-gray-300 px-4 py-2">{{ $contract->created_at->format('d.m.y') }}
                                </td>
                                <td class="border border-gray-300 px-4 py-2">{{ $contract->user->first_name }}
                                    {{ $contract->user->last_name }}</td>
                                <td class="border border-gray-300 px-4 py-2 text-blue-700">
                                    <a href="{{ route('admin.contract.show', $contract->id) }}">
                                        {{ $contract->number }}
                                    </a>
                                </td>
                                <td class="border border-gray-300 px-4 py-2">
                                    {{ $contract->client->name }}
                                </td>
                                <td class="border border-gray-300 px-4 py-2">
                                    {{ $contract->client->company }}
                                </td>
                                <td class="border border-gray-300 px-4 py-2">
                                    {{ $contract->client->phone }}
                                </td>
                                <td class="border border-gray-300 px-4 py-2">
                                    @foreach ($contract->services as $service)
                                        {{ $service->name }} 
                                        @if (!$loop->last)
                                        ,
                                        @endif
                                    @endforeach
                                </td>
                                <td class="border border-gray-300 px-4 py-2">
                                    {{ $contract->getPrice() }}
                                </td>
                                @foreach ($contract->payments as $payment)
                                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap {{ $payment->status == 'close' ? 'bg-green-500 text-white' : '' }}">
                                        {{ $payment->getFormatValue() }}
                                    </td>
                                @endforeach

                                @for ($i = count($contract->payments); $i < 5; $i++)
                                    <td class="border border-gray-300 px-4 py-2"></td>
                                @endfor
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
