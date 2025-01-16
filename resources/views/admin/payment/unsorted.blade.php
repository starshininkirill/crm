@extends('admin.layouts.payment')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">Неразобранные платежи</h1>
    <div class="">
        @if ($payments->isEmpty())
            <h2>Платежей не найдено</h2>
        @else
            <table class="min-w-full border border-gray-300">
                <thead>
                    <tr class="bg-gray-800">
                        <th class="border border-gray-300 px-4 py-2 text-left text-white">ИП</th>
                        <th class="border border-gray-300 px-4 py-2 text-left text-white">Номер</th>
                        <th class="border border-gray-300 px-4 py-2 text-left text-white">Сумма</th>
                        <th class="border border-gray-300 px-4 py-2 text-left text-white">Обоснование</th>
                        <th class="border border-gray-300 px-4 py-2 text-left text-white">ИНН</th>
                        <th class="border border-gray-300 px-4 py-2 text-left text-white">Дата</th>
                        <th class="border border-gray-300 px-4 py-2 text-left text-white">Разделить</th>
                        <th class="border border-gray-300 px-4 py-2 text-left text-white">Прикрепить</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $payment)
                        <tr class="">
                            <td class="border border-gray-300 px-4 py-2">

                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <a href="{{ route('admin.payment.show', $payment->id) }}" class=" text-blue-700 underline">
                                    № {{ $payment->id }}
                                </a>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ $payment->getFormatValue() }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                Описание
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                @if ($payment->inn)
                                    {{ $payment->inn }}
                                @endif
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ $payment->created_at->format('Y-m-d H:i:s') }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <span>
                                    Прикрепить
                                </span>
                                <span>
                                    Разделить
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
